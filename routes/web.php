<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

// Public routes (no login required)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/aspirasi', [HomeController::class, 'publicFeed'])->name('public.feed');

Route::get('/lapor', [ReportController::class, 'create'])->name('reports.create');
Route::post('/lapor', [ReportController::class, 'store'])->name('reports.store');
Route::get('/lapor/sukses/{ticketCode}', [ReportController::class, 'success'])->name('reports.success');

Route::get('/lacak', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/lacak', [TrackingController::class, 'lookup'])->name('tracking.lookup');
Route::get('/lacak/{ticketCode}', [TrackingController::class, 'show'])->name('tracking.show');
Route::post('/lacak/{ticketCode}/reply', [TrackingController::class, 'reply'])->name('tracking.reply');

// Admin authentication
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/laporan', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan-export', [AdminReportController::class, 'export'])->name('reports.export');
        Route::get('/laporan/{report}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::post('/laporan/{report}/respon', [AdminReportController::class, 'respond'])->name('reports.respond');
        Route::get('/laporan/{report}/edit', [AdminReportController::class, 'edit'])->name('reports.edit');
        Route::put('/laporan/{report}', [AdminReportController::class, 'update'])->name('reports.update');
        Route::delete('/laporan/{report}', [AdminReportController::class, 'destroy'])->name('reports.destroy');

        Route::get('/kategori', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/kategori', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/kategori/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/kategori/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/akun', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/akun', [AdminUserController::class, 'store'])->name('users.store');
        Route::put('/akun/password', [AdminUserController::class, 'updatePassword'])->name('users.password');
        Route::delete('/akun/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
