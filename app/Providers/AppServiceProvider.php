<?php

namespace App\Providers;

use App\Services\SiakadService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Opsional: jika ingin singleton
        $this->app->singleton(SiakadService::class, function ($app) {
            return new SiakadService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // return true to allow viewing the Log Viewer.
        LogViewer::auth(function ($request) {
            return $request->user() && $request->user()->hasAnyRole(['superadmin', 'developer']);
        });

        // define viewing the Log Viewer ke view
        Gate::define('view-log-sistem', function ($user) {
            return $user->hasAnyRole(['superadmin', 'developer']);
        });
    }
}
