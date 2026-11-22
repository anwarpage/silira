<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total' => Report::count(),
            'menunggu' => Report::where('status', 'menunggu')->count(),
            'diverifikasi' => Report::where('status', 'diverifikasi')->count(),
            'diproses' => Report::where('status', 'diproses')->count(),
            'selesai' => Report::where('status', 'selesai')->count(),
            'ditolak' => Report::where('status', 'ditolak')->count(),
        ];

        $categoryStats = Category::withCount('reports')
            ->orderByDesc('reports_count')
            ->get();

        $jenisStats = collect(Report::JENIS_OPTIONS)->mapWithKeys(fn (string $jenis) => [
            $jenis => Report::where('jenis_laporan', $jenis)->count(),
        ]);

        $recentReports = Report::with('category')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'categoryStats', 'jenisStats', 'recentReports'));
    }
}
