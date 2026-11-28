<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function publicReport(Request $request, string $ticketCode): StreamedResponse
    {
        $report = Report::where('ticket_code', strtoupper(trim($ticketCode)))->firstOrFail();

        $this->authorizePublicAccess($request, $report);

        abort_unless($report->attachment_path, 404);

        return $this->serveFile($report->attachment_path, $report->attachment_name);
    }

    public function publicResponse(Request $request, string $ticketCode, ReportResponse $response): StreamedResponse
    {
        $report = Report::where('ticket_code', strtoupper(trim($ticketCode)))->firstOrFail();

        abort_if($response->report_id !== $report->id, 404);
        abort_if($response->is_internal, 403);

        $this->authorizePublicAccess($request, $report);

        abort_unless($response->attachment_path, 404);

        return $this->serveFile($response->attachment_path, $response->attachment_name);
    }

    public function adminReport(Report $report): StreamedResponse
    {
        abort_unless($report->attachment_path, 404);

        return $this->serveFile($report->attachment_path, $report->attachment_name);
    }

    public function adminResponse(ReportResponse $response): StreamedResponse
    {
        abort_unless($response->attachment_path, 404);

        return $this->serveFile($response->attachment_path, $response->attachment_name);
    }

    private function authorizePublicAccess(Request $request, Report $report): void
    {
        if (! $report->isConfidential()) {
            return;
        }

        if ($request->user() !== null) {
            return;
        }

        $unlocked = (bool) $request->session()->get("unlocked_tickets.{$report->ticket_code}", false);
        abort_unless($unlocked, 403, 'Masukkan PIN akses tiket terlebih dahulu untuk melihat lampiran rahasia.');
    }

    private function serveFile(string $path, ?string $originalName): StreamedResponse
    {
        $disk = Storage::disk('local')->exists($path)
            ? 'local'
            : (Storage::disk('public')->exists($path) ? 'public' : null);

        abort_unless($disk !== null, 404);

        $filename = $originalName ?: basename($path);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($isImage) {
            return Storage::disk($disk)->response($path, $filename, $headers);
        }

        return Storage::disk($disk)->download($path, $filename, $headers);
    }
}
