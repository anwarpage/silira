<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total' => Report::count(),
            'menunggu' => Report::whereIn('status', ['menunggu', 'diverifikasi'])->count(),
            'diproses' => Report::where('status', 'diproses')->count(),
            'selesai' => Report::where('status', 'selesai')->count(),
        ];

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $publicReports = Report::with('category')
            ->withCount(['publicResponses'])
            ->visiblePublicly()
            ->latest()
            ->take(6)
            ->get();

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
}
