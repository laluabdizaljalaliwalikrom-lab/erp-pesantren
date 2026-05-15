<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->disk('public')
                    ->square()
                    ->label('Gambar'),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Judul'),
                    
                TextColumn::make('category')
                    ->badge()
                    ->label('Kategori'),
                    
                ToggleColumn::make('is_published')
                    ->label('Publish'),
                    
                TextColumn::make('published_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->label('Waktu Publish'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Kegiatan' => 'Kegiatan',
                        'Prestasi' => 'Prestasi',
                        'Warta' => 'Warta',
                        'Artikel' => 'Artikel',
                    ])
                    ->label('Kategori'),
                TernaryFilter::make('is_published')
                    ->label('Status Publish'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
