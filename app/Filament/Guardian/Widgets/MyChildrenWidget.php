<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Widgets;

use App\Models\Student;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class MyChildrenWidget extends Widget
{
    protected string $view = 'filament.guardian.widgets.my-children';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 2;

    public function getChildren(): Collection
    {
        return Student::query()
            ->with(['academic.schoolClass', 'academic.institution'])
            ->where('guardian_id', auth('guardian')->id())
            ->get();
    }
}
