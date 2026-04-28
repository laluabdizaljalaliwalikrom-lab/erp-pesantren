<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\BillResource\Pages;

use App\Filament\Guardian\Resources\BillResource;
use Filament\Resources\Pages\ListRecords;

class ListBills extends ListRecords
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
