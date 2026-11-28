<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReportResponse;
use App\Services\ReportAnalyticsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $reports = $this->filteredQuery($request)
            ->with('category')
            ->withCount('responses')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        $availableYears = Report::query()
            ->selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([(int) now()->format('Y')]);
        }

        return view('admin.reports.index', compact('reports', 'categories', 'availableYears'));
    }

    public function show(Report $report): View
    {
        $report->load(['category', 'responses.user']);

        return view('admin.reports.show', compact('report'));
    }

    public function respond(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Report::STATUSES))],
            'message' => ['required', 'string', 'min:3', 'max:3000'],
            'is_internal' => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'message.required' => 'Isi tanggapan atau catatan tindak lanjut wajib diisi.',
        ]);

        $isInternal = $request->boolean('is_internal');
        $oldStatus = $report->status;
        $newStatus = $isInternal ? $oldStatus : $validated['status'];

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('attachments/responses', 'local');
            $attachmentName = $file->getClientOriginalName();
        }

        try {
            DB::transaction(function () use ($request, $report, $validated, $isInternal, $oldStatus, $newStatus, $attachmentPath, $attachmentName): void {
                if (! $isInternal && $newStatus !== $oldStatus) {
                    $report->update($this->statusLifecycleUpdates($report, $newStatus));
                }

                ReportResponse::create([
                    'report_id' => $report->id,
                    'user_id' => $request->user()->id,
                    'sender_type' => 'admin',
                    'sender_name' => $request->user()->name,
                    'message' => $validated['message'],
                    'attachment_path' => $attachmentPath,
                    'attachment_name' => $attachmentName,
                    'status_from' => (! $isInternal && $newStatus !== $oldStatus) ? $oldStatus : null,
                    'status_to' => (! $isInternal && $newStatus !== $oldStatus) ? $newStatus : null,
                    'is_internal' => $isInternal,
                ]);
            });
        } catch (Throwable $e) {
            if ($attachmentPath) {
                Storage::disk('local')->delete($attachmentPath);
            }

            throw $e;
        }

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', $isInternal ? 'Catatan internal DPM berhasil disimpan.' : 'Tanggapan dan status laporan berhasil diperbarui.');
    }

    public function edit(Report $report): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.reports.edit', compact('report', 'categories'));
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_laporan' => ['required', Rule::in(Report::JENIS_OPTIONS)],
            'category_id' => ['required', 'exists:categories,id'],
            'privacy' => ['required', Rule::in(array_keys(Report::PRIVACY_OPTIONS))],
            'status' => ['required', Rule::in(array_keys(Report::STATUSES))],
            'nama_pelapor' => ['required', 'string', 'max:120'],
            'nim_nip' => ['required', 'string', 'max:60'],
            'jurusan_instansi' => ['nullable', 'string', 'max:120'],
            'no_wa' => ['required', 'string', 'max:25', 'regex:/^[0-9+\-\s()]{8,25}$/'],
            'judul_laporan' => ['required', 'string', 'max:255'],
            'isi_laporan' => ['required', 'string', 'max:3000'],
            'tanggal_kejadian' => ['nullable', 'date'],
            'lokasi_kejadian' => ['nullable', 'string', 'max:180'],
        ], [
            'no_wa.regex' => 'Format nomor WhatsApp tidak valid (gunakan angka 8–25 karakter).',
        ]);

        $oldStatus = $report->status;
        $newStatus = $validated['status'];

        DB::transaction(function () use ($request, $report, $validated, $oldStatus, $newStatus): void {
            $payload = $validated;

            if ($newStatus !== $oldStatus) {
                $payload = array_merge($payload, $this->statusLifecycleUpdates($report, $newStatus));
            }

            $report->update($payload);

            if ($newStatus !== $oldStatus) {
                $statusLabel = Report::STATUSES[$newStatus] ?? $newStatus;
                ReportResponse::create([
                    'report_id' => $report->id,
                    'user_id' => $request->user()->id,
                    'sender_type' => 'admin',
                    'sender_name' => $request->user()->name,
                    'message' => "Status laporan diperbarui menjadi {$statusLabel} melalui pembaruan data laporan.",
                    'status_from' => $oldStatus,
                    'status_to' => $newStatus,
                    'is_internal' => false,
                ]);
            }
        });

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', 'Data laporan berhasil diperbarui.');
    }

    public function destroy(Report $report): RedirectResponse
    {
        $report->load('responses');

        $paths = collect([$report->attachment_path])
            ->merge($report->responses->pluck('attachment_path'))
            ->filter()
            ->unique()
            ->values();

        $ticketCode = $report->ticket_code;

        DB::transaction(function () use ($report, $paths): void {
            $report->delete();

            DB::afterCommit(function () use ($paths): void {
                foreach ($paths as $path) {
                    Storage::disk('local')->delete($path);
                    Storage::disk('public')->delete($path);
                }
            });
        });

        return redirect()
            ->route('admin.reports.index')
            ->with('success', "Laporan {$ticketCode} berhasil dihapus.");
    }

    public function export(Request $request, ReportAnalyticsService $analytics): StreamedResponse
    {
        $query = $this->filteredQuery($request)
            ->with('category')
            ->withCount('responses')
            ->latest();

        $filename = 'laporan-silira-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query, $analytics): void {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for clean Excel display
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Kode Tiket',
                'Waktu Masuk',
                'Jenis Laporan',
                'Kategori Bidang',
                'Privasi',
                'Status',
                'Nama Pelapor',
                'NIM/NIP',
                'Jurusan/Instansi',
                'No WhatsApp',
                'Judul Laporan',
                'Isi Laporan',
                'Tanggal Kejadian',
                'Lokasi Kejadian',
                'Lampiran',
                'Waktu Verifikasi',
                'Waktu Selesai',
                'Jumlah Tanggapan',
            ]);

            foreach ($query->cursor() as $report) {
                fputcsv($handle, array_map([$analytics, 'sanitizeSpreadsheetCell'], [
                    $report->ticket_code,
                    $report->created_at?->format('Y-m-d H:i:s') ?? '-',
                    $report->jenis_laporan,
                    $report->category?->name ?? '-',
                    $report->privacy_label,
                    $report->status_label,
                    $report->nama_pelapor,
                    $report->nim_nip,
                    $report->jurusan_instansi ?? '-',
                    $report->no_wa,
                    $report->judul_laporan,
                    $report->isi_laporan,
                    $report->tanggal_kejadian?->format('Y-m-d') ?? '-',
                    $report->lokasi_kejadian ?? '-',
                    $report->attachment_name ?? '-',
                    $report->verified_at?->format('Y-m-d H:i:s') ?? '-',
                    $report->resolved_at?->format('Y-m-d H:i:s') ?? '-',
                    (string) $report->responses_count,
                ]));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function statusLifecycleUpdates(Report $report, string $newStatus): array
    {
        $updates = ['status' => $newStatus];

        if (in_array($newStatus, ['diverifikasi', 'diproses', 'selesai'], true) && ! $report->verified_at) {
            $updates['verified_at'] = now();
        }

        if (in_array($newStatus, ['selesai', 'ditolak'], true)) {
            $updates['resolved_at'] = now();
        } else {
            $updates['resolved_at'] = null;
        }

        return $updates;
    }

    private function filteredQuery(Request $request): Builder
    {
        $query = Report::query();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', "%{$search}%")
                    ->orWhere('nama_pelapor', 'like', "%{$search}%")
                    ->orWhere('nim_nip', 'like', "%{$search}%")
                    ->orWhere('judul_laporan', 'like', "%{$search}%")
                    ->orWhere('isi_laporan', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($jenis = $request->input('jenis')) {
            $query->where('jenis_laporan', $jenis);
        }

        if ($privacy = $request->input('privacy')) {
            $query->where('privacy', $privacy);
        }

        if ($month = $request->input('month')) {
            $query->whereMonth('created_at', (int) $month);
        }

        if ($year = $request->input('year')) {
            $query->whereYear('created_at', (int) $year);
        }

        return $query;
    }
}
