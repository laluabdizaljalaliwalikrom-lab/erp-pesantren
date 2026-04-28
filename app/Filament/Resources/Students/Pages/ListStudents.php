<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Student;
use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Enums\ResidencyStatus;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import_dapodik')
                ->label('Import dari Dapodik (Excel)')
                ->icon('heroicon-o-document-chart-bar')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel Dapodik')
                        ->disk('local')
                        ->directory('imports')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv'
                        ])
                        ->required(),
                ])
                ->action(function (array $data, Action $action) {
                    $filePath = Storage::disk('local')->path($data['file']);
                    
                    try {
                        $rows = SimpleExcelReader::create($filePath)->getRows();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Gagal membaca file')
                            ->body($e->getMessage())
                            ->send();
                        return;
                    }

                    $successCount = 0;
                    $failedCount = 0;

                    $rows->each(function (array $row) use (&$successCount, &$failedCount) {
                        try {
                            $nisn = $row['Nomor Induk Siswa Nasional'] ?? null;
                            
                            // Skip if no NISN as it's our unique identifier
                            if (empty($nisn)) {
                                $failedCount++;
                                return;
                            }

                            // Gender mapping
                            $genderRaw = $row['Jenis Kelamin'] ?? null;
                            $gender = Gender::MALE; // default
                            if (stripos((string)$genderRaw, 'P') !== false || stripos((string)$genderRaw, 'Perempuan') !== false) {
                                $gender = Gender::FEMALE;
                            }

                            // Date parsing
                            $dobRaw = $row['Tanggal Lahir'] ?? null;
                            $dob = null;
                            if ($dobRaw) {
                                try {
                                    $dob = \Carbon\Carbon::parse($dobRaw)->format('Y-m-d');
                                } catch (\Exception $e) {
                                    $dob = null;
                                }
                            }

                            Student::updateOrCreate(
                                ['nisn' => $nisn],
                                [
                                    'full_name' => $row['Nama Lengkap'] ?? 'Tanpa Nama',
                                    'nis' => $row['NIS'] ?? $row['Nomor Induk'] ?? null,
                                    'birth_place' => $row['Tempat Lahir'] ?? null,
                                    'birth_date' => $dob,
                                    'gender' => $gender,
                                    'mother_name' => $row['Nama Ibu Kandung'] ?? null,
                                    // Default values for required fields or specific setup
                                    'status' => StudentStatus::ACTIVE,
                                    'residency_status' => ResidencyStatus::NON_RESIDENT,
                                ]
                            );

                            $successCount++;
                        } catch (\Exception $e) {
                            $failedCount++;
                        }
                    });

                    // Cleanup uploaded file
                    Storage::disk('local')->delete($data['file']);

                    if ($failedCount > 0) {
                        Notification::make()
                            ->warning()
                            ->title('Import Selesai dengan Catatan')
                            ->body("{$successCount} Data berhasil, {$failedCount} Data gagal karena format rusak atau NISN kosong.")
                            ->send();
                    } else {
                        Notification::make()
                            ->success()
                            ->title('Import Berhasil')
                            ->body("{$successCount} Data santri berhasil diimport dari Dapodik.")
                            ->send();
                    }
                }),

            CreateAction::make(),
        ];
    }
}
