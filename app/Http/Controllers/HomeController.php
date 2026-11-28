<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $statusCounts = Report::query()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $stats = [
            'total' => (int) $statusCounts->sum(),
            'menunggu' => (int) (($statusCounts['menunggu'] ?? 0) + ($statusCounts['diverifikasi'] ?? 0)),
            'diproses' => (int) ($statusCounts['diproses'] ?? 0),
            'selesai' => (int) ($statusCounts['selesai'] ?? 0),
        ];

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $publicReports = Report::with('category')
            ->withCount(['publicResponses'])
            ->visiblePublicly()
            ->latest()
            ->take(5)
            ->get();

        if ($featured = $publicReports->first()) {
            $featured->load('publicResponses');
        }

        return view('public.home', compact('stats', 'categories', 'publicReports'));
    }

    public function publicFeed(Request $request): View
    {
        $query = Report::with('category')
            ->withCount(['publicResponses'])
            ->visiblePublicly();

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_laporan', 'like', "%{$search}%")
                    ->orWhere('isi_laporan', 'like', "%{$search}%")
                    ->orWhere('ticket_code', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($jenis = $request->input('jenis')) {
            $query->where('jenis_laporan', $jenis);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $reports = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('public.feed', compact('reports', 'categories'));
    }

    public function showPublicReport(string $ticketCode): View
    {
        $code = strtoupper(trim($ticketCode));

        $report = Report::with(['category', 'publicResponses'])
            ->visiblePublicly()
            ->where('ticket_code', $code)
            ->firstOrFail();

        return view('public.aspiration-show', compact('report'));
    }
}
