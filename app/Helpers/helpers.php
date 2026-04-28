<?php

declare(strict_types=1);

if (! function_exists('settings')) {
    /**
     * Get the application settings.
     *
     * @return \App\Models\AppSetting
     */
    function settings()
    {
        return app(\App\Services\SettingsService::class)->get();
    }
}
