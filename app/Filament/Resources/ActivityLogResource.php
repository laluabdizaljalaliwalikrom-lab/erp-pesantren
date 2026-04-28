<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use BackedEnum;
use UnitEnum;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;

class ActivityLogResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = \App\Models\Activity::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-finger-print';

    protected static UnitEnum|string|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return 'Log Aktivitas';
    }

    public static function getModelLabel(): string
    {
        return 'Log Aktivitas';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Log Aktivitas';
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Detail Aktivitas')
                    ->schema([
                        Forms\Components\TextInput::make('causer.name')
                            ->label('Oleh')
                            ->readOnly(),
                        Forms\Components\TextInput::make('description')
                            ->label('Aksi')
                            ->readOnly(),
                        Forms\Components\TextInput::make('subject_type')
                            ->label('Tipe Subjek')
                            ->readOnly(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Waktu')
                            ->readOnly(),
                    ])->columns(2),
                Section::make('Perubahan')
                    ->schema([
                        Forms\Components\KeyValue::make('properties.attributes')
                            ->label('Nilai Baru'),
                        Forms\Components\KeyValue::make('properties.old')
                            ->label('Nilai Lama'),
                    ])->visible(fn ($record) => $record && $record->properties->count() > 0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('causer.name')
                    ->label('Oleh')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Sistem'),

                TextColumn::make('description')
                    ->label('Melakukan Apa')
                    ->searchable(),

                TextColumn::make('subject_type')
                    ->label('Tipe Subjek')
                    ->formatStateUsing(function ($state, Activity $record) {
                        $model = strtolower(class_basename($state));
                        $label = match ($model) {
                            'student' => 'Santri',
                            'payment' => 'Pembayaran',
                            'bill'    => 'Tagihan',
                            'user'    => 'Pengguna',
                            default   => $model,
                        };
                        return $label;
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('Tipe')
                    ->options([
                        'user' => 'Pengguna',
                        'student' => 'Santri',
                        'payment' => 'Pembayaran',
                        'bill' => 'Tagihan',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
