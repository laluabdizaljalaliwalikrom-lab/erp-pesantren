<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Str;
use App\Models\Post;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten Berita')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('Judul Berita')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        
                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(Post::class, 'slug', ignoreRecord: true)
                            ->label('Slug / URL'),

                        Select::make('category')
                            ->options([
                                'Kegiatan' => 'Kegiatan',
                                'Prestasi' => 'Prestasi',
                                'Warta' => 'Warta',
                                'Artikel' => 'Artikel',
                            ])
                            ->required()
                            ->label('Kategori'),
                            
                        RichEditor::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->label('Isi Berita'),
                            
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('image_path')
                                    ->image()
                                    ->directory('news')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->label('Gambar Utama')
                                    ->columnSpanFull(),

                                Toggle::make('is_published')
                                    ->default(true)
                                    ->label('Publikasikan'),
                                    
                                DateTimePicker::make('published_at')
                                    ->default(now())
                                    ->label('Tanggal Publikasi'),
                            ]),
                    ])
            ]);
    }
}
