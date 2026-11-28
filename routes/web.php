<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

// Public routes (no login required)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/aspirasi', [HomeController::class, 'publicFeed'])->name('public.feed');
Route::get('/aspirasi/{ticketCode}', [HomeController::class, 'showPublicReport'])->name('public.aspirations.show');

Route::get('/lapor', [ReportController::class, 'create'])->name('reports.create');
Route::post('/lapor', [ReportController::class, 'store'])
    ->middleware('throttle:report-submit')
    ->name('reports.store');
Route::get('/lapor/sukses/{ticketCode}', [ReportController::class, 'success'])->name('reports.success');
Route::get('/lapor/sukses/{ticketCode}/unduh', [ReportController::class, 'downloadReceipt'])->name('reports.receipt');

Route::get('/lacak', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/lacak', [TrackingController::class, 'lookup'])
    ->middleware('throttle:ticket-lookup')
    ->name('tracking.lookup');
Route::get('/lacak/{ticketCode}', [TrackingController::class, 'show'])->name('tracking.show');
Route::post('/lacak/{ticketCode}/unlock', [TrackingController::class, 'unlock'])
    ->middleware('throttle:ticket-unlock')
    ->name('tracking.unlock');
Route::post('/lacak/{ticketCode}/reply', [TrackingController::class, 'reply'])
    ->middleware('throttle:ticket-reply')
    ->name('tracking.reply');

Route::get('/lacak/{ticketCode}/lampiran', [AttachmentController::class, 'publicReport'])
    ->name('tracking.attachment');
Route::get('/lacak/{ticketCode}/respon/{response}/lampiran', [AttachmentController::class, 'publicResponse'])
    ->name('tracking.response.attachment');

// Admin authentication
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])
            ->middleware('throttle:admin-login')
            ->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/analitik/pertanyaan', [AdminDashboardController::class, 'questionsData'])->name('analytics.questions');
        Route::get('/analitik/individual', [AdminDashboardController::class, 'individualData'])->name('analytics.individual');
        Route::get('/analitik/export/csv', [AdminDashboardController::class, 'exportCsv'])->name('analytics.export.csv');
        Route::get('/analitik/export/excel', [AdminDashboardController::class, 'exportExcel'])->name('analytics.export.excel');
        Route::get('/analitik/export/pdf', [AdminDashboardController::class, 'exportPdf'])->name('analytics.export.pdf');
        Route::get('/analitik/respons/{report}/pdf', [AdminDashboardController::class, 'exportIndividualPdf'])->name('analytics.response.pdf');

        Route::get('/laporan', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan-export', [AdminReportController::class, 'export'])->name('reports.export');
        Route::get('/laporan/{report}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::get('/laporan/{report}/lampiran', [AttachmentController::class, 'adminReport'])->name('reports.attachment');
        Route::get('/respon/{response}/lampiran', [AttachmentController::class, 'adminResponse'])->name('responses.attachment');
        Route::post('/laporan/{report}/respon', [AdminReportController::class, 'respond'])->name('reports.respond');
        Route::get('/laporan/{report}/edit', [AdminReportController::class, 'edit'])->name('reports.edit');
        Route::put('/laporan/{report}', [AdminReportController::class, 'update'])->name('reports.update');
        Route::delete('/laporan/{report}', [AdminReportController::class, 'destroy'])->name('reports.destroy');

        Route::get('/kategori', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/kategori', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/kategori/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/kategori/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/akun', [AdminUserController::class, 'index'])->name('users.index');
        Route::put('/akun/password', [AdminUserController::class, 'updatePassword'])->name('users.password');

        Route::middleware('superadmin')->group(function () {
            Route::post('/akun', [AdminUserController::class, 'store'])->name('users.store');
            Route::delete('/akun/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        });
    });
});
