<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\AcademicStatus;
use App\Enums\StudentStatus;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAcademic;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class AcademicManagement extends Page implements HasTable
{
    use InteractsWithTable;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Santri';

    protected static ?string $navigationLabel = 'Kenaikan & Kelulusan';

    protected static ?string $title = 'Manajemen Kenaikan & Kelulusan';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.academic-management';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::query()
                    ->where('status', StudentStatus::ACTIVE)
                    ->with(['academic.schoolClass'])
            )
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->disk('public'),

                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Student $record): string => $record->nisn ?? 'No NISN'),

                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('active_classes')
                    ->label('Kelas Aktif')
                    ->state(fn (Student $record): array => 
                        $record->academicRecords()
                            ->where('status', AcademicStatus::ACTIVE)
                            ->with('schoolClass')
                            ->get()
                            ->pluck('schoolClass.name')
                            ->filter()
                            ->toArray()
                    )
                    ->badge()
                    ->color('info')
                    ->placeholder('Belum ada kelas'),
            ])
            ->filters([
                SelectFilter::make('class')
                    ->label('Filter Kelas')
                    ->options(SchoolClass::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return $query->whereHas('academicRecords', fn ($q) => 
                            $q->where('school_class_id', $data['value'])
                              ->where('status', AcademicStatus::ACTIVE)
                        );
                    }),
            ])
            ->actions([
                Action::make('promote')
                    ->label('Naik Kelas')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->color('success')
                    ->form([
                        Select::make('source_academic_id')
                            ->label('Pilih Kelas Saat Ini')
                            ->options(fn (Student $record) => 
                                $record->academicRecords()
                                    ->where('status', AcademicStatus::ACTIVE)
                                    ->with('schoolClass')
                                    ->get()
                                    ->pluck('schoolClass.name', 'id')
                            )
                            ->reactive()
                            ->required(),
                        Select::make('new_class_id')
                            ->label('Pilih Kelas Tujuan')
                            ->options(function (callable $get) {
                                $sourceAcademicId = $get('source_academic_id');
                                if (!$sourceAcademicId) return [];
                                
                                $sourceAcademic = StudentAcademic::find($sourceAcademicId);
                                if (!$sourceAcademic || !$sourceAcademic->institution) return [];
                                
                                $type = $sourceAcademic->institution->type;
                                
                                return SchoolClass::whereHas('institution', fn ($q) => $q->where('type', $type))
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->hint('Hanya menampilkan kelas dengan tipe institusi yang sama (Formal/Non-Formal)'),
                    ])
                    ->action(function (Student $record, array $data): void {
                        $academic = StudentAcademic::find($data['source_academic_id']);
                        $newClass = SchoolClass::find($data['new_class_id']);
                        
                        if ($academic && $newClass) {
                            // Secondary check
                            if ($academic->institution->type !== $newClass->institution->type) {
                                Notification::make()
                                    ->danger()
                                    ->title('Gagal Validasi')
                                    ->body('Kelas formal hanya bisa pindah ke kelas formal, dan sebaliknya.')
                                    ->send();
                                return;
                            }

                            $academic->update(['school_class_id' => $data['new_class_id']]);
                            
                            Notification::make()
                                ->success()
                                ->title('Berhasil Naik Kelas')
                                ->body("Santri {$record->full_name} berhasil dipindahkan ke kelas baru.")
                                ->send();
                        }
                    })
                    ->requiresConfirmation(),

                Action::make('graduate')
                    ->label('Luluskan')
                    ->icon('heroicon-o-academic-cap')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (Student $record): void {
                        $record->update([
                            'status' => StudentStatus::GRADUATED,
                            'graduation_year' => date('Y'),
                        ]);
                        
                        $activeRecords = $record->academicRecords()->where('status', AcademicStatus::ACTIVE)->get();
                        foreach ($activeRecords as $academic) {
                            $academic->update([
                                'status' => AcademicStatus::GRADUATED,
                                'school_class_id' => null,
                            ]);
                        }

                        Notification::make()
                            ->success()
                            ->title('Santri Lulus')
                            ->body("Ananda {$record->full_name} telah resmi menjadi alumni.")
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('promote')
                        ->label('Naik Kelas')
                        ->icon('heroicon-o-arrow-trending-up')
                        ->color('success')
                        ->form([
                            Placeholder::make('selected_students')
                                ->label('Santri Terpilih')
                                ->content(fn (Collection $records): HtmlString => new HtmlString(
                                    '<div class="max-h-40 overflow-y-auto p-2 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">' .
                                    $records->pluck('full_name')->map(fn ($name) => "• {$name}")->implode('<br>') .
                                    '</div>'
                                )),
                            Select::make('source_class_id')
                                ->label('Dari Kelas')
                                ->options(SchoolClass::all()->pluck('name', 'id'))
                                ->searchable()
                                ->reactive()
                                ->required(),
                            Select::make('new_class_id')
                                ->label('Pilih Kelas Tujuan')
                                ->options(function (callable $get) {
                                    $sourceClassId = $get('source_class_id');
                                    if (!$sourceClassId) return [];
                                    
                                    $sourceClass = SchoolClass::find($sourceClassId);
                                    if (!$sourceClass || !$sourceClass->institution) return [];
                                    
                                    $type = $sourceClass->institution->type;
                                    
                                    return SchoolClass::whereHas('institution', fn ($q) => $q->where('type', $type))
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->required()
                                ->hint('Hanya menampilkan kelas dengan tipe institusi yang sama'),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $newClass = SchoolClass::find($data['new_class_id']);
                            if (!$newClass) return;

                            $className = $newClass->name;
                            $count = 0;

                            foreach ($records as $record) {
                                $academic = $record->academicRecords()
                                    ->where('status', AcademicStatus::ACTIVE)
                                    ->where('school_class_id', $data['source_class_id'])
                                    ->first();
                                    
                                if ($academic) {
                                    // Validation check
                                    if ($academic->institution->type === $newClass->institution->type) {
                                        $academic->update(['school_class_id' => $data['new_class_id']]);
                                        $count++;
                                    }
                                }
                            }

                            Notification::make()
                                ->success()
                                ->title('Berhasil Naik Kelas')
                                ->body("Berhasil memindahkan {$count} santri ke kelas {$className}")
                                ->send();
                        })
                        ->requiresConfirmation(),

                    BulkAction::make('graduate')
                        ->label('Luluskan Santri')
                        ->icon('heroicon-o-academic-cap')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Kelulusan Massal')
                        ->modalDescription('Apakah Anda yakin ingin meluluskan santri-santri yang dipilih? Status akan berubah menjadi alumni dan kelas akan dikosongkan.')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'status' => StudentStatus::GRADUATED,
                                    'graduation_year' => date('Y'),
                                ]);
                                
                                $activeRecords = $record->academicRecords()->where('status', AcademicStatus::ACTIVE)->get();
                                foreach ($activeRecords as $academic) {
                                    $academic->update([
                                        'status' => AcademicStatus::GRADUATED,
                                        'school_class_id' => null,
                                    ]);
                                }
                            }

                            Notification::make()
                                ->success()
                                ->title('Kelulusan Berhasil')
                                ->body(count($records) . " santri telah berhasil diluluskan.")
                                ->send();
                        }),
                ]),
            ]);
    }
}
