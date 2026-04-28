<?php

declare(strict_types=1);

namespace App\Filament\Resources\Academics\Pages;

use App\Filament\Resources\Academics\AcademicResource;
use App\Models\Institution;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Services\StudentAcademicService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;

class ListAcademics extends ListRecords
{
    protected static string $resource = AcademicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            
            Action::make('bulk_assign')
                ->label('Penempatan Massal')
                ->icon('heroicon-o-users')
                ->color('primary')
                ->form([
                    Select::make('institution_id')
                        ->label('Lembaga')
                        ->options(Institution::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live(),
                        
                    Select::make('school_class_id')
                        ->label('Kelas')
                        ->options(fn (Get $get) => filled($get('institution_id')) ? SchoolClass::where('institution_id', $get('institution_id'))->pluck('name', 'id') : [])
                        ->required()
                        ->searchable()
                        ->preload(),
                        
                    Select::make('academic_year_id')
                        ->label('Tahun Ajaran')
                        ->options(AcademicYear::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),
                        
                    Select::make('semester')
                        ->label('Semester')
                        ->options([
                            1 => 'Ganjil (1)',
                            2 => 'Genap (2)',
                        ])
                        ->required(),
                        
                    CheckboxList::make('student_ids')
                        ->label('Santri')
                        ->options(Student::where('status', 'active')->pluck('full_name', 'id'))
                        ->descriptions(function () {
                            $classes = SchoolClass::pluck('name', 'id');
                            $institutions = Institution::pluck('name', 'id');
                            
                            return Student::where('status', 'active')
                                ->with(['academicRecords' => fn($q) => $q->where('status', \App\Enums\AcademicStatus::ACTIVE)])
                                ->get()
                                ->mapWithKeys(function ($student) use ($classes, $institutions) {
                                    if ($student->academicRecords->isEmpty()) {
                                        return [$student->id => 'Belum ada kelas aktif'];
                                    }
                                    
                                    $desc = $student->academicRecords->map(function($record) use ($classes, $institutions) {
                                        $inst = $institutions[$record->institution_id] ?? '-';
                                        $cls = $classes[$record->school_class_id] ?? '-';
                                        return "{$inst} - {$cls}";
                                    })->join(' | ');
                                    
                                    return [$student->id => "Aktif di: {$desc}"];
                                })
                                ->toArray();
                        })
                        ->columns(2)
                        ->searchable()
                        ->bulkToggleable()
                        ->required(),
                ])
                ->action(function (array $data, StudentAcademicService $service): void {
                    $assignedCount = $service->bulkAssign($data);

                    Notification::make()
                        ->title('Proses Selesai')
                        ->body("Berhasil menempatkan {$assignedCount} santri.")
                        ->success()
                        ->send();
                }),
        ];
    }
}