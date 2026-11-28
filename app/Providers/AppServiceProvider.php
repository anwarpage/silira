<?php

namespace App\Providers;

use App\Models\Report;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('admin-login', function (Request $request) {
            $email = strtolower(trim((string) $request->input('email')));

            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });

        RateLimiter::for('report-submit', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->ip());
        });

        RateLimiter::for('ticket-lookup', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('ticket-reply', function (Request $request) {
            return Limit::perMinutes(10, 10)->by($request->ip());
        });

        RateLimiter::for('ticket-unlock', function (Request $request) {
            $code = strtoupper(trim((string) $request->route('ticketCode')));

            return Limit::perMinutes(10, 10)->by($request->ip().'|'.$code);
        });

        View::composer('layouts.admin', function (\Illuminate\View\View $view): void {
            $view->with('pendingCount', Report::where('status', 'menunggu')->count());
        });
    }
}
