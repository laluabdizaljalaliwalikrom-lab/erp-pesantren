<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Get the settings instance, cached for performance.
     */
    public function get(): AppSetting
    {
        try {
            $settings = Cache::get('app_settings');

            if ($settings instanceof \__PHP_Incomplete_Class || ! $settings instanceof AppSetting) {
                $this->refresh();
                $settings = AppSetting::instance();
                Cache::forever('app_settings', $settings);
            }

            return $settings;
        } catch (\Exception $e) {
            // Return a dummy instance if database is not available (e.g. during build)
            return new AppSetting([
                'pesantren_name' => config('app.name', 'ERP Pesantren'),
                'primary_color' => '#10b981', // Default emerald
            ]);
        }
    }

    /**
     * Clear the settings cache.
     */
    public function refresh(): void
    {
        Cache::forget('app_settings');
    }
}
