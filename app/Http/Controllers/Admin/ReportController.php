<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReportResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        return view('admin.reports.index', compact('reports', 'categories'));
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
            $attachmentPath = $file->store('attachments/responses', 'public');
            $attachmentName = $file->getClientOriginalName();
        }

        if (! $isInternal && $newStatus !== $oldStatus) {
            $updates = ['status' => $newStatus];
            if ($newStatus === 'diverifikasi' && ! $report->verified_at) {
                $updates['verified_at'] = now();
            }
            if (in_array($newStatus, ['selesai', 'ditolak'], true)) {
                $updates['resolved_at'] = now();
            }
            $report->update($updates);
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
            'no_wa' => ['required', 'string', 'max:25'],
            'judul_laporan' => ['required', 'string', 'max:255'],
            'isi_laporan' => ['required', 'string', 'max:3000'],
            'tanggal_kejadian' => ['nullable', 'date'],
            'lokasi_kejadian' => ['nullable', 'string', 'max:180'],
        ]);

        $report->update($validated);

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', 'Data laporan berhasil diperbarui.');
    }

    public function destroy(Report $report): RedirectResponse
    {
        if ($report->attachment_path) {
            Storage::disk('public')->delete($report->attachment_path);
        }

        foreach ($report->responses as $response) {
            if ($response->attachment_path) {
                Storage::disk('public')->delete($response->attachment_path);
            }
        }

        $ticketCode = $report->ticket_code;
        $report->delete();

        return redirect()
            ->route('admin.reports.index')
            ->with('success', "Laporan {$ticketCode} berhasil dihapus.");
    }

    public function export(Request $request): StreamedResponse
    {
        $reports = $this->filteredQuery($request)
            ->with('category')
            ->latest()
            ->get();

        $filename = 'laporan-silira-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($reports) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for clean Excel display
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Kode Tiket',
                'Jenis Laporan',
                'Kategori',
                'Privasi',
                'Status',
                'Nama Pelapor',
                'NIM/NIP',
                'Jurusan/Instansi',
                'No WA',
                'Judul Laporan',
                'Isi Laporan',
                'Tanggal Kejadian',
                'Lokasi Kejadian',
                'Waktu Masuk',
            ]);

            foreach ($reports as $report) {
                fputcsv($handle, [
                    $report->ticket_code,
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
                    $report->created_at?->format('d-m-Y H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
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
