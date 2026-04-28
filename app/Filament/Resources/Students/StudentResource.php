<?php

declare(strict_types=1);

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Santri';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'Santri Aktif';
    }

    public static function getModelLabel(): string
    {
        return 'Santri Aktif';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Santri Aktif';
    }

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', '!=', \App\Enums\StudentStatus::GRADUATED);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', \App\Enums\StudentStatus::ACTIVE)->count();
    }

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \AlizHarb\ActivityLog\RelationManagers\ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}
