<?php

declare(strict_types=1);

namespace App\Filament\Resources\Fees\Pages;

use App\Enums\ResidencyStatus;
use App\Filament\Resources\Fees\FeeResource;
use App\Models\AcademicYear;
use App\Models\Fee;
use App\Models\Institution;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\BillingService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;

class ListFees extends ListRecords
{
    protected static string $resource = FeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            
            Action::make('generate_mass_bills')
                ->label('Picu Tagihan Massal')
                ->icon('heroicon-o-document-duplicate')
                ->color('primary')
                ->form([
                    Select::make('fee_id')
                        ->label('Biaya')
                        ->options(Fee::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),
                        
                    Select::make('institution_id')
                        ->label('Lembaga')
                        ->options(Institution::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->live()
                        ->placeholder('Semua Lembaga'),
                        
                    Select::make('school_class_id')
                        ->label('Kelas')
                        ->options(fn (Get $get) => filled($get('institution_id')) ? SchoolClass::where('institution_id', $get('institution_id'))->pluck('name', 'id') : [])
                        ->searchable()
                        ->preload()
                        ->placeholder('Semua Kelas'),
                        
                    Select::make('student_id')
                        ->label('Santri')
                        ->options(Student::where('status', 'active')->pluck('full_name', 'id'))
                        ->searchable()
                        ->preload()
                        ->placeholder('Semua Santri'),
                        
                    Select::make('academic_year_id')
                        ->label('Tahun Ajaran')
                        ->options(AcademicYear::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),
                        
                    Select::make('residency_status')
                        ->label('Status Mukim')
                        ->options(ResidencyStatus::class)
                        ->placeholder('Semua Santri'),
                ])
                ->action(function (array $data, BillingService $service): void {
                    $generatedCount = $service->generateMassBills($data);

                    Notification::make()
                        ->title('Proses Tagihan Selesai')
                        ->body("Berhasil membuat {$generatedCount} tagihan.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
