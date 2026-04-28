<?php

declare(strict_types=1);

namespace App\Filament\Resources\Guardians\Tables;

use App\Models\Guardian;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class GuardiansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nik')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->copyable()
                    ->color('success'),
                TextColumn::make('registration_nis_request')
                    ->label('NIS Request')
                    ->searchable(),
                TextColumn::make('is_approved')
                    ->label('Status')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Approved' : 'Pending'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Action::make('chat_wa')
                    ->label('Chat WA')
                    ->color('success')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->url(fn (Guardian $record): string => 'https://wa.me/62' . preg_replace('/^(\+62|62|0)/', '', $record->phone_number))
                    ->openUrlInNewTab(),
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-m-check-circle')
                    ->visible(fn (Guardian $record): bool => !$record->is_approved)
                    ->requiresConfirmation()
                    ->action(function (Guardian $record) {
                        $record->update(['is_approved' => true]);

                        if ($record->registration_nis_request) {
                            $student = \App\Models\Student::where('nis', $record->registration_nis_request)->first();
                            if ($student) {
                                $student->update(['guardian_id' => $record->id]);
                            }
                        }

                        Notification::make()
                            ->title('Wali berhasil disetujui')
                            ->body('Akun telah aktif dan data santri telah ditautkan.')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
