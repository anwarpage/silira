<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($code = trim((string) $request->input('code'))) {
            return redirect()->route('tracking.show', ['ticketCode' => strtoupper($code)]);
        }

        return view('public.track', ['report' => null, 'searchedCode' => null]);
    }

    public function lookup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_code' => ['required', 'string', 'max:32'],
        ], [
            'ticket_code.required' => 'Masukkan kode tiket terlebih dahulu.',
        ]);

        $code = strtoupper(trim($validated['ticket_code']));

        return redirect()->route('tracking.show', ['ticketCode' => $code]);
    }

    public function show(string $ticketCode): View
    {
        $code = strtoupper(trim($ticketCode));

        $report = Report::with(['category', 'publicResponses'])
            ->where('ticket_code', $code)
            ->first();

        return view('public.track', [
            'report' => $report,
            'searchedCode' => $code,
        ]);
    }

    public function reply(Request $request, string $ticketCode): RedirectResponse
    {
        $code = strtoupper(trim($ticketCode));

        $report = Report::where('ticket_code', $code)->firstOrFail();

        if (! $report->canReporterReply()) {
            return back()->with('error', 'Tiket ini sudah ditutup sehingga tidak dapat menerima tanggapan baru.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:5', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'message.required' => 'Tanggapan tidak boleh kosong.',
            'message.min' => 'Tanggapan minimal 5 karakter.',
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('attachments/replies', 'public');
            $attachmentName = $file->getClientOriginalName();
        }

        ReportResponse::create([
            'report_id' => $report->id,
            'sender_type' => 'reporter',
            'sender_name' => $report->privacy === 'anonymous' ? 'Pelapor (Anonim)' : $report->nama_pelapor,
            'message' => $validated['message'],
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'is_internal' => false,
        ]);

        return redirect()
            ->route('tracking.show', ['ticketCode' => $report->ticket_code])
            ->with('success', 'Tanggapan tambahan Anda telah dikirim ke Admin DPM.');
    }
}
