<?php

declare(strict_types=1);

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Tables\AlumniTable;
use App\Models\Student;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AlumniResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-check-badge';

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Santri';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Data Alumni';
    }

    public static function getModelLabel(): string
    {
        return 'Alumni';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Alumni';
    }

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', \App\Enums\StudentStatus::GRADUATED);
    }

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlumniTable::configure($table);
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
            'index' => Pages\ListAlumni::route('/'),
            'edit' => Pages\EditAlumni::route('/{record}/edit'),
        ];
    }
}
