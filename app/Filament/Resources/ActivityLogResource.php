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
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;

class ActivityLogResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = \App\Models\Activity::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-finger-print';

    protected static string|null|\UnitEnum $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 4;

    protected static ?string $label = 'Activity Log';

    protected static ?string $pluralLabel = 'Activity Logs';

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
                Section::make('Activity Details')
                    ->schema([
                        Forms\Components\TextInput::make('causer.name')
                            ->label('Who')
                            ->readOnly(),
                        Forms\Components\TextInput::make('description')
                            ->label('Action')
                            ->readOnly(),
                        Forms\Components\TextInput::make('subject_type')
                            ->label('Subject Type')
                            ->readOnly(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->readOnly(),
                    ])->columns(2),
                Section::make('Changes')
                    ->schema([
                        Forms\Components\KeyValue::make('properties.attributes')
                            ->label('New Values'),
                        Forms\Components\KeyValue::make('properties.old')
                            ->label('Old Values'),
                    ])->visible(fn ($record) => $record && $record->properties->count() > 0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('causer.name')
                    ->label('Who')
                    ->sortable()
                    ->searchable()
                    ->placeholder('System'),

                TextColumn::make('description')
                    ->label('Did What')
                    ->searchable(),

                TextColumn::make('subject_type')
                    ->label('To Whom (Subject)')
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
                    ->label('When')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('Type')
                    ->options([
                        'user' => 'Users',
                        'student' => 'Students',
                        'payment' => 'Payments',
                        'bill' => 'Bills',
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
