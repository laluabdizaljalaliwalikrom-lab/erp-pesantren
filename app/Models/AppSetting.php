<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AppSetting extends Model
{
    use HasUuids;

    protected $fillable = [
        'pesantren_name',
        'hero_title',
        'hero_subtitle',
        'vision',
        'mission',
        'history',
        'leader_name',
        'address',
        'phone',
        'email',
        'logo_path',
        'favicon_path',
        'footer_text',
        'primary_color',
    ];

    /**
     * Get the single instance of settings.
     */
    public static function instance(): self
    {
        return static::first() ?: static::create([
            'pesantren_name' => 'ERP Pesantren',
        ]);
    }
}
