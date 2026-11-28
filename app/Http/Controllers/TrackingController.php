<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class TrackingController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($code = trim((string) $request->input('code'))) {
            return redirect()->route('tracking.show', ['ticketCode' => strtoupper($code)]);
        }

        return view('public.track', [
            'report' => null,
            'searchedCode' => null,
            'isLockedConfidential' => false,
            'isPinUnlocked' => false,
        ]);
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

    public function show(Request $request, string $ticketCode): View
    {
        $code = strtoupper(trim($ticketCode));

        $report = Report::with('category')
            ->where('ticket_code', $code)
            ->first();

        $isPinUnlocked = $report !== null
            && (bool) $request->session()->get("unlocked_tickets.{$report->ticket_code}", false);

        $isLockedConfidential = $report !== null
            && $report->isConfidential()
            && ! $isPinUnlocked
            && $request->user() === null;

        if ($report && ! $isLockedConfidential) {
            $report->load('publicResponses');
        }

        return view('public.track', [
            'report' => $report,
            'searchedCode' => $code,
            'isLockedConfidential' => $isLockedConfidential,
            'isPinUnlocked' => $isPinUnlocked,
        ]);
    }

    public function unlock(Request $request, string $ticketCode): RedirectResponse
    {
        $code = strtoupper(trim($ticketCode));
        $report = Report::where('ticket_code', $code)->firstOrFail();

        $validated = $request->validate([
            'access_pin' => ['required', 'string', 'max:20'],
        ], [
            'access_pin.required' => 'Masukkan PIN akses tiket terlebih dahulu.',
        ]);

        if (! $report->verifyAccessPin($validated['access_pin'])) {
            return back()
                ->withErrors(['access_pin' => 'PIN akses tidak sesuai dengan kode tiket ini.'])
                ->withInput();
        }

        $request->session()->put("unlocked_tickets.{$report->ticket_code}", true);

        return redirect()
            ->route('tracking.show', ['ticketCode' => $report->ticket_code])
            ->with('success', 'Akses tiket berhasil diverifikasi.');
    }

    public function reply(Request $request, string $ticketCode): RedirectResponse
    {
        $code = strtoupper(trim($ticketCode));

        $report = Report::where('ticket_code', $code)->firstOrFail();

        if (! $report->canReporterReply()) {
            return back()->with('error', 'Tiket ini sudah ditutup sehingga tidak dapat menerima tanggapan baru.');
        }

        $isSessionUnlocked = (bool) $request->session()->get("unlocked_tickets.{$report->ticket_code}", false);

        $rules = [
            'message' => ['required', 'string', 'min:5', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ];

        if (! $isSessionUnlocked) {
            $rules['access_pin'] = ['required', 'string', 'max:20'];
        }

        $validated = $request->validate($rules, [
            'access_pin.required' => 'PIN akses tiket wajib diisi untuk mengirim tanggapan.',
            'message.required' => 'Tanggapan tidak boleh kosong.',
            'message.min' => 'Tanggapan minimal 5 karakter.',
        ]);

        if (! $isSessionUnlocked) {
            if (! $report->verifyAccessPin($validated['access_pin'] ?? null)) {
                return back()
                    ->withErrors(['access_pin' => 'PIN akses tidak sesuai. Hanya pemilik tiket yang dapat mengirim tanggapan.'])
                    ->withInput($request->only('message'));
            }

            $request->session()->put("unlocked_tickets.{$report->ticket_code}", true);
        }

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('attachments/replies', 'local');
            $attachmentName = $file->getClientOriginalName();
        }

        $senderName = match ($report->privacy) {
            'anonymous' => 'Pelapor (Anonim)',
            'confidential' => 'Pelapor (Rahasia)',
            default => $report->nama_pelapor,
        };

        try {
            ReportResponse::create([
                'report_id' => $report->id,
                'sender_type' => 'reporter',
                'sender_name' => $senderName,
                'message' => $validated['message'],
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'is_internal' => false,
            ]);
        } catch (Throwable $e) {
            if ($attachmentPath) {
                Storage::disk('local')->delete($attachmentPath);
            }

            throw $e;
        }

        return redirect()
            ->route('tracking.show', ['ticketCode' => $report->ticket_code])
            ->with('success', 'Tanggapan tambahan Anda telah dikirim ke Admin DPM.');
    }
}
