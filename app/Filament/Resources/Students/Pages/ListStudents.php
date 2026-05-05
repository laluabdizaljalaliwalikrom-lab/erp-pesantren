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
                        $headerRowNumber = 5;
                        $found = false;
                        
                        $tempRows = SimpleExcelReader::create($filePath)->noHeaderRow()->getRows();
                        foreach ($tempRows as $index => $row) {
                            $rowString = implode(' ', array_values($row));
                            if (stripos($rowString, 'NISN') !== false && stripos($rowString, 'Nama') !== false) {
                                $headerRowNumber = $index + 1;
                                $found = true;
                                break;
                            }
                        }

                        $rows = SimpleExcelReader::create($filePath)
                            ->headerOnRow($headerRowNumber - 1)
                            ->getRows()
                            ->skip(1);
                            
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
                    $skippedCount = 0;
                    $errorLog = [];

                    $rows->each(function (array $row, $index) use (&$successCount, &$failedCount, &$skippedCount, &$errorLog) {
                        try {
                            $nisn = $row['NISN'] ?? null;
                            
                            if (empty($nisn) || $nisn === 'NISN') {
                                if (!empty($row['Nama'])) {
                                    $failedCount++;
                                    $errorLog[] = "Baris " . ($index + 7) . ": NISN Kosong";
                                }
                                return;
                            }

                            // CEK VALIDASI: Jika NISN sudah ada, skip (jangan sampai dobel)
                            if (Student::where('nisn', $nisn)->exists()) {
                                $skippedCount++;
                                return;
                            }

                            // Gender mapping
                            $genderRaw = $row['JK'] ?? null;
                            $gender = Gender::MALE;
                            if (stripos((string)$genderRaw, 'P') !== false) {
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

                            // Helper untuk membersihkan data
                            $clean = fn($val) => (empty(trim((string)$val)) ? null : trim((string)$val));

                            Student::create([
                                'nisn'             => $nisn,
                                'full_name'        => $clean($row['Nama']) ?? 'Tanpa Nama',
                                'nis'              => $clean($row['NIPD']) ?? $clean($row['NIS']) ?? null,
                                'nik'              => $clean($row['NIK']) ?? null,
                                'no_kk'            => $clean($row['No KK']) ?? null,
                                'birth_place'      => $clean($row['Tempat Lahir']) ?? null,
                                'birth_date'       => $dob,
                                'gender'           => $gender,
                                'address'          => $clean($row['Alamat']) ?? null,
                                'rt'               => $clean($row['RT']) ?? null,
                                'rw'               => $clean($row['RW']) ?? null,
                                'dusun'            => $clean($row['Dusun']) ?? null,
                                'village'          => $clean($row['Kelurahan']) ?? null,
                                'district'         => $clean($row['Kecamatan']) ?? null,
                                'father_name'      => $clean($row['Data Ayah']) ?? null,
                                'mother_name'      => $clean($row['Data Ibu']) ?? null,
                                'hp'               => $clean($row['HP']) ?? $clean($row['Telepon']) ?? null,
                                'email'            => $clean($row['E-Mail']) ?? null,
                                'previous_school'  => $clean($row['Sekolah Asal']) ?? null,
                                'sibling_position' => $clean($row['Anak ke-berapa']) ?? null,
                                'sibling_count'    => $clean($row['Jml. Saudara Kandung']) ?? $clean($row["Jml. Saudara\nKandung"]) ?? null,
                                'status'           => StudentStatus::ACTIVE,
                                'residency_status' => ResidencyStatus::TIDAK_MUKIM,
                            ]);

                            $successCount++;
                        } catch (\Exception $e) {
                            $failedCount++;
                            $errorLog[] = "Baris " . ($index + 7) . " (" . ($row['Nama'] ?? 'N/A') . "): " . $e->getMessage();
                        }
                    });

                    // Cleanup
                    Storage::disk('local')->delete($data['file']);

                    if ($successCount > 0 || $skippedCount > 0) {
                        $body = "{$successCount} Data baru diimport.";
                        if ($skippedCount > 0) {
                            $body .= " {$skippedCount} Data dilewati (sudah ada).";
                        }
                        
                        Notification::make()
                            ->success()
                            ->title('Proses Selesai')
                            ->body($body)
                            ->send();
                    }

                    if ($failedCount > 0) {
                        Notification::make()
                            ->warning()
                            ->title('Ada Data Gagal')
                            ->body("{$failedCount} Gagal. Log: " . implode(', ', array_slice($errorLog, 0, 1)))
                            ->send();
                    }
                }),

            CreateAction::make(),
        ];
    }
}
