<?php

declare(strict_types=1);

namespace App\Filament\Resources\Students\Schemas;

use App\Models\Student;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Carbon;

class StudentView
{
    public static function configure(Schema $infolist): Schema
    {
        return $infolist
            ->schema([
                Grid::make(3)
                    ->schema([
                        // Sidebar: Photo & Primary Status
                        Group::make([
                            Section::make()
                                ->schema([
                                    ImageEntry::make('photo')
                                        ->label('')
                                        ->circular()
                                        ->disk('public')
                                        ->extraImgAttributes([
                                            'class' => 'ring-4 ring-primary-500/10 transition hover:ring-primary-500/20',
                                        ])
                                        ->placeholder('No Photo')
                                        ->alignCenter()
                                        ->size(160),

                                    TextEntry::make('full_name')
                                        ->label('')
                                        ->weight(FontWeight::Bold)
                                        ->size(TextSize::Large)
                                        ->alignCenter(),

                                    TextEntry::make('status')
                                        ->label('')
                                        ->badge()
                                        ->alignCenter(),
                                ]),
                        ])->columnSpan(1),

                        // Main Content: Details
                        Group::make([
                            // Profile Section
                            Section::make('Identitas Santri')
                                ->icon('heroicon-o-identification')
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('nis')
                                        ->label('NIS')
                                        ->copyable()
                                        ->icon('heroicon-m-qr-code'),

                                    TextEntry::make('nisn')
                                        ->label('NISN')
                                        ->copyable()
                                        ->placeholder('-'),

                                    TextEntry::make('gender')
                                        ->label('Jenis Kelamin')
                                        ->formatStateUsing(fn ($state) => $state?->getLabel()),

                                    TextEntry::make('birth_date')
                                        ->label('Tempat/Tgl Lahir')
                                        ->state(fn ($record) => ($record->birth_place ?? '-') . ', ' . ($record->birth_date ? Carbon::parse($record->birth_date)->format('d F Y') : '-'))
                                        ->suffix(fn ($record) => $record->birth_date ? ' (' . Carbon::parse($record->birth_date)->age . ' tahun)' : ''),
                                ]),

                            // Contact & Location
                            Section::make('Kontak & Alamat')
                                ->icon('heroicon-o-map-pin')
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('address')
                                        ->label('Alamat Lengkap')
                                        ->columnSpanFull(),

                                    TextEntry::make('guardian_phone_override')
                                        ->label('No. HP / WA')
                                        ->state(fn ($record) => $record->guardian_phone_override ?? $record->guardian?->phone ?? '-')
                                        ->icon('heroicon-m-phone')
                                        ->copyable(),
                                ]),

                            // Parents info
                            Section::make('Data Orang Tua / Wali')
                                ->icon('heroicon-o-users')
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('father_name')
                                        ->label('Ayah Kandung')
                                        ->placeholder('-'),

                                    TextEntry::make('mother_name')
                                        ->label('Ibu Kandung')
                                        ->placeholder('-'),

                                    TextEntry::make('guardian.name')
                                        ->label('Wali Terdaftar')
                                        ->placeholder('Belum ditautkan'),
                                ]),

                            // Academic Status
                            Section::make('Status Akademik (Aktif)')
                                ->icon('heroicon-o-academic-cap')
                                ->schema([
                                    RepeatableEntry::make('academicRecords')
                                        ->label('')
                                        ->columns(3)
                                        ->schema([
                                            TextEntry::make('institution.name')
                                                ->label('Lembaga')
                                                ->weight(FontWeight::Bold),
                                            TextEntry::make('schoolClass.name')
                                                ->label('Kelas')
                                                ->badge()
                                                ->color('primary'),
                                            TextEntry::make('status')
                                                ->label('Status')
                                                ->badge(),
                                        ]),
                                ]),
                        ])->columnSpan(2),
                    ]),
            ]);
    }
}
