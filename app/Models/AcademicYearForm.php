<?php

declare(strict_types=1);

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Tahun Ajaran')
                ->placeholder('e.g., 2025/2026')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            DatePicker::make('start_date')
                ->label('Tanggal Mulai')
                ->required(),
            DatePicker::make('end_date')
                ->label('Tanggal Selesai')
                ->after('start_date')
                ->required(),
            Toggle::make('is_active')
                ->label('Aktifkan Tahun Ajaran Ini')
                ->helperText('Mengaktifkan ini akan menonaktifkan tahun ajaran lain yang sedang aktif.'),
        ]);
    }
}