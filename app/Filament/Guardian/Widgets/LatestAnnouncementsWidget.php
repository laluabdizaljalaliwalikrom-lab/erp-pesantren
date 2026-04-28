<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Widgets;

use App\Models\Announcement;
use Filament\Widgets\Widget;

class LatestAnnouncementsWidget extends Widget
{
    protected string $view = 'filament.guardian.widgets.latest-announcements';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 0;

    protected function getViewData(): array
    {
        return [
            'announcements' => Announcement::where('is_active', true)
                ->where(function($query) {
                    $query->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
        ];
    }
}
