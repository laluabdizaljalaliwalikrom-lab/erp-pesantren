<?php

declare(strict_types=1);

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use App\Models\AcademicYear;
use App\Models\Institution;
use App\Models\SchoolClass;
use App\Enums\StudentStatus;
use App\Enums\AcademicStatus;
use App\Enums\Gender;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Detail Santri')
                    ->tabs([
                        Tab::make('Identitas Pribadi')
                            ->icon('heroicon-m-user')
                            ->schema([
                                FileUpload::make('photo')
                                    ->label('Foto Santri')
                                    ->image()
                                    ->directory('students/photos')
                                    ->columnSpanFull(),

                                TextInput::make('full_name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->prefixIcon('heroicon-m-user'),

                                TextInput::make('nik')
                                    ->label('NIK')
                                    ->length(16)
                                    ->prefixIcon('heroicon-m-identification'),

                                TextInput::make('no_kk')
                                    ->label('No. KK')
                                    ->length(16)
                                    ->prefixIcon('heroicon-m-identification'),

                                TextInput::make('nis')
                                    ->label('NIS')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->prefixIcon('heroicon-m-identification'),

                                TextInput::make('nisn')
                                    ->label('NISN')
                                    ->unique(ignoreRecord: true)
                                    ->prefixIcon('heroicon-m-identification'),

                                Select::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->options(Gender::class)
                                    ->required()
                                    ->native(false),

                                TextInput::make('birth_place')
                                    ->label('Tempat Lahir')
                                    ->required(),

                                DatePicker::make('birth_date')
                                    ->label('Tanggal Lahir')
                                    ->required()
                                    ->native(false),

                                TextInput::make('sibling_position')
                                    ->label('Anak Ke')
                                    ->numeric(),

                                TextInput::make('sibling_count')
                                    ->label('Jumlah Saudara')
                                    ->numeric(),

                                Select::make('status')
                                    ->label('Status Santri')
                                    ->options(StudentStatus::class)
                                    ->default(StudentStatus::ACTIVE)
                                    ->required()
                                    ->native(false),

                                Select::make('residency_status')
                                    ->label('Status Mukim')
                                    ->options([
                                        'mukim' => 'Mukim',
                                        'tidak_mukim' => 'Non-Mukim',
                                    ])
                                    ->required()
                                    ->native(false),

                                Select::make('special_condition')
                                    ->label('Kondisi Khusus')
                                    ->options([
                                        'none' => 'Reguler',
                                        'yatim' => 'Yatim',
                                        'piatu' => 'Piatu',
                                        'yatim_piatu' => 'Yatim Piatu',
                                        'kurang_mampu' => 'Kurang Mampu',
                                    ])
                                    ->default('none')
                                    ->required()
                                    ->native(false),
                            ])->columns(2),

                        Tab::make('Alamat & Kontak')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                Textarea::make('address')
                                    ->label('Alamat Lengkap')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),

                                TextInput::make('rt')
                                    ->label('RT')
                                    ->maxLength(5),

                                TextInput::make('rw')
                                    ->label('RW')
                                    ->maxLength(5),

                                TextInput::make('dusun')
                                    ->label('Dusun/Lingkungan'),

                                TextInput::make('village')
                                    ->label('Kelurahan/Desa'),

                                TextInput::make('district')
                                    ->label('Kecamatan'),

                                TextInput::make('hp')
                                    ->label('No. HP Santri')
                                    ->tel()
                                    ->prefixIcon('heroicon-m-phone'),

                                TextInput::make('email')
                                    ->label('Email Santri')
                                    ->email()
                                    ->prefixIcon('heroicon-m-envelope'),
                            ])->columns(2),

                        Tab::make('Orang Tua / Wali')
                            ->icon('heroicon-m-users')
                            ->schema([
                                Select::make('guardian_id')
                                    ->label('Pilih Wali Terdaftar')
                                    ->relationship(name: 'guardian', titleAttribute: 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Cari wali berdasarkan nama...')
                                    ->columnSpanFull(),

                                TextInput::make('father_name')
                                    ->label('Nama Ayah')
                                    ->prefixIcon('heroicon-m-user'),

                                TextInput::make('father_nik')
                                    ->label('NIK Ayah')
                                    ->length(16),

                                TextInput::make('mother_name')
                                    ->label('Nama Ibu')
                                    ->prefixIcon('heroicon-m-user'),

                                TextInput::make('mother_nik')
                                    ->label('NIK Ibu')
                                    ->length(16),

                                TextInput::make('guardian_phone')
                                    ->label('No. HP Wali (Override)')
                                    ->tel()
                                    ->columnSpanFull()
                                    ->helperText('Kosongkan jika ingin menggunakan No. HP dari data wali utama'),
                            ])->columns(2),

                        Tab::make('Akademik')
                            ->icon('heroicon-m-academic-cap')
                            ->schema([
                                TextInput::make('previous_school')
                                    ->label('Sekolah Asal')
                                    ->columnSpanFull()
                                    ->prefixIcon('heroicon-m-building-library'),

                                Repeater::make('academicRecords')
                                    ->relationship('academicRecords')
                                    ->label('Riwayat / Status Akademik')
                                    ->maxItems(2)
                                    ->schema([
                                        Select::make('institution_id')
                                            ->label('Lembaga')
                                            ->options(Institution::all()->pluck('name', 'id'))
                                            ->required()
                                            ->searchable()
                                            ->live(),

                                        Select::make('school_class_id')
                                            ->label('Kelas')
                                            ->options(fn (Get $get) => SchoolClass::where('institution_id', $get('institution_id'))->pluck('name', 'id'))
                                            ->required()
                                            ->searchable()
                                            ->disabled(fn (Get $get) => ! $get('institution_id')),

                                        Select::make('academic_year_id')
                                            ->label('Tahun Akademik')
                                            ->options(AcademicYear::all()->pluck('name', 'id'))
                                            ->default(fn () => AcademicYear::where('is_active', true)->first()?->id)
                                            ->required(),

                                        TextInput::make('semester')
                                            ->label('Semester')
                                            ->numeric()
                                            ->default(1)
                                            ->required(),

                                        Select::make('status')
                                            ->label('Status')
                                            ->options(AcademicStatus::class)
                                            ->default(AcademicStatus::ACTIVE)
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->itemLabel(fn (array $state): ?string => Institution::find($state['institution_id'] ?? null)?->name ?? 'Informasi Akademik'),
                            ]),

                        Tab::make('Data Kelulusan')
                            ->icon('heroicon-m-check-badge')
                            ->schema([
                                TextInput::make('graduation_year')
                                    ->label('Tahun Lulus')
                                    ->numeric()
                                    ->length(4),
                                TextInput::make('after_graduation_status')
                                    ->label('Status Pasca Lulus')
                                    ->placeholder('Contoh: Kuliah di Al-Azhar, Bekerja, dll'),
                            ])
                            ->visible(fn ($get) => $get('status') === StudentStatus::GRADUATED->value || $get('status') === StudentStatus::GRADUATED),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
