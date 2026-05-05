<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        config([
            'filament-activity-log.resource.class' => \App\Filament\Resources\ActivityLogResource::class,
            'filament-activity-log.widgets.enabled' => false,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super Admin Bypass
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        \Illuminate\Support\Facades\Gate::policy(
            \App\Models\Activity::class,
            \App\Policies\ActivityLogPolicy::class
        );

        if (str_contains(config('app.url'), 'ngrok-free.app')) {
            URL::forceScheme('https');
        }
    }
}
