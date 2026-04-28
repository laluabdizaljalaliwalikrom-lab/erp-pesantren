<?php

declare(strict_types=1);

namespace App\Filament\Resources\Academics\Schemas;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AcademicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label('Siswa')
                    ->relationship('student', 'full_name') // Using full_name as inferred from context
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('institution_id')
                    ->label('Lembaga')
                    ->relationship('institution', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('class_name', null))
                    ->required(),

                Select::make('academic_year')
                    ->label('Tahun Ajaran')
                    ->options(fn () => AcademicYear::pluck('name', 'id'))
                    ->default(fn () => AcademicYear::where('is_active', true)->value('id'))
                    ->searchable()
                    ->required(),

                Select::make('semester')
                    ->label('Semester')
                    ->options([
                        1 => 'Ganjil (1)',
                        2 => 'Genap (2)',
                    ])
                    ->required(),

                Select::make('class_name')
                    ->label('Kelas')
                    ->options(function (Get $get) {
                        if (! $get('institution_id')) {
                            return [];
                        }
                        return SchoolClass::where('institution_id', $get('institution_id'))->pluck('name', 'name');
                    })
                    ->searchable()
                    ->required(),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}