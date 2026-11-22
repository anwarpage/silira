<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Report;
use App\Models\ReportResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('public.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_laporan' => ['required', Rule::in(Report::JENIS_OPTIONS)],
            'category_id' => ['required', 'exists:categories,id'],
            'privacy' => ['required', Rule::in(array_keys(Report::PRIVACY_OPTIONS))],
            'nama_pelapor' => ['required', 'string', 'max:120'],
            'nim_nip' => ['required', 'string', 'max:60'],
            'jurusan_instansi' => ['nullable', 'string', 'max:120'],
            'no_wa' => ['required', 'string', 'max:25'],
            'judul_laporan' => ['required', 'string', 'max:255'],
            'isi_laporan' => ['required', 'string', 'min:20', 'max:3000'],
            'tanggal_kejadian' => ['nullable', 'date', 'before_or_equal:today'],
            'lokasi_kejadian' => ['nullable', 'string', 'max:180'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'isi_laporan.min' => 'Isi laporan minimal 20 karakter agar dapat ditindaklanjuti dengan jelas.',
            'attachment.max' => 'Ukuran lampiran maksimal 5 MB.',
            'attachment.mimes' => 'Format lampiran yang didukung: JPG, PNG, WEBP, atau PDF.',
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
        }

        $report = Report::create([
            'ticket_code' => Report::generateTicketCode(),
            'jenis_laporan' => $validated['jenis_laporan'],
            'category_id' => $validated['category_id'],
            'privacy' => $validated['privacy'],
            'nama_pelapor' => $validated['nama_pelapor'],
            'nim_nip' => $validated['nim_nip'],
            'jurusan_instansi' => $validated['jurusan_instansi'] ?? null,
            'no_wa' => $validated['no_wa'],
            'judul_laporan' => $validated['judul_laporan'],
            'isi_laporan' => $validated['isi_laporan'],
            'tanggal_kejadian' => $validated['tanggal_kejadian'] ?? null,
            'lokasi_kejadian' => $validated['lokasi_kejadian'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'status' => 'menunggu',
        ]);

        $privacyNote = match ($report->privacy) {
            'anonymous' => ' Identitas Anda disembunyikan (Anonim) pada daftar publik.',
            'confidential' => ' Laporan bersifat Rahasia dan tidak ditampilkan pada daftar publik.',
            default => '',
        };

        ReportResponse::create([
            'report_id' => $report->id,
            'sender_type' => 'system',
            'sender_name' => 'Sistem SILIRA',
            'message' => "Laporan berhasil diterima oleh sistem dan menunggu verifikasi DPM FDIK.{$privacyNote}",
            'status_to' => 'menunggu',
        ]);

        return redirect()
            ->route('reports.success', ['ticketCode' => $report->ticket_code])
            ->with('submitted_ticket', $report->ticket_code);
    }

    public function success(string $ticketCode): View
    {
        $report = Report::with('category')
            ->where('ticket_code', strtoupper(trim($ticketCode)))
            ->firstOrFail();

        return view('public.success', compact('report'));
    }
}
