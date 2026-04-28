<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Announcement Details')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('Judul Pengumuman'),
                        
                        \Filament\Forms\Components\Select::make('category')
                            ->options([
                                'Info' => 'Info',
                                'Urgent' => 'Urgent',
                                'Agenda' => 'Agenda',
                            ])
                            ->required()
                            ->label('Kategori'),
                            
                        \Filament\Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->label('Konten Pengumuman'),
                            
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\Toggle::make('is_active')
                                    ->default(true)
                                    ->label('Status Aktif'),
                                    
                                \Filament\Forms\Components\DateTimePicker::make('published_at')
                                    ->default(now())
                                    ->label('Tanggal Publikasi'),
                            ]),
                    ])
            ]);
    }
}
