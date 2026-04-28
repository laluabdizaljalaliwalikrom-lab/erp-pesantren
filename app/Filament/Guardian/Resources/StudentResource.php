<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources;

use App\Models\Student;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\Resource;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Data Santri';

    protected static ?string $modelLabel = 'Santri';

    protected static UnitEnum|string|null $navigationGroup = null;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['guardian', 'academic.schoolClass', 'academic.institution'])
            ->where('guardian_id', auth('guardian')->id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->disk('public'),

                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->nis ?? '-'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('academic.institution.name')
                    ->label('Lembaga')
                    ->placeholder('-'),

                TextColumn::make('academic.schoolClass.name')
                    ->label('Kelas')
                    ->badge()
                    ->color('primary')
                    ->placeholder('-'),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Lihat Detail')
                    ->infolist(fn (Student $record, Schema $infolist) => $infolist->schema([
                        \Filament\Schemas\Components\Section::make('Identitas Santri')
                            ->schema([
                                ImageEntry::make('photo')
                                    ->label('Foto')
                                    ->disk('public')
                                    ->circular()
                                    ->size(100),
                                TextEntry::make('full_name')->label('Nama Lengkap'),
                                TextEntry::make('nis')->label('NIS')->copyable(),
                                TextEntry::make('gender')->label('Jenis Kelamin')
                                    ->formatStateUsing(fn ($state) => $state?->getLabel()),
                                TextEntry::make('birth_date')->label('Tgl Lahir')->date(),
                                TextEntry::make('address')->label('Alamat')->columnSpanFull(),
                                TextEntry::make('status')->label('Status')->badge(),
                                TextEntry::make('academic.institution.name')->label('Lembaga')->placeholder('-'),
                                TextEntry::make('academic.schoolClass.name')->label('Kelas')->badge()->color('primary')->placeholder('-'),
                            ])->columns(2),
                    ]))
                    ->modalHeading('Detail Santri')
                    ->modalWidth('2xl'),
            ])
            ->paginated([10, 25])
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading('Belum Ada Santri')
            ->emptyStateDescription('Tidak ada data santri yang terhubung dengan akun Anda.');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Guardian\Resources\StudentResource\Pages\ListStudents::route('/'),
        ];
    }
}
