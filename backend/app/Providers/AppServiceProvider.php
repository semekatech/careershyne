<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
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
        Schema::defaultStringLength(191);
        RateLimiter::for('ai-upload', function ($request) {
            return Limit::perMinute(2)->by($request->ip())->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Slow down! Only 2 uploads allowed per minute.'
                ], 429);
            });
        });
    }
}
