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
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Section;
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
                ->modalWidth('5xl')
                ->form([
                    Wizard::make([
                        Step::make('Unggah File')
                            ->description('Pilih file Excel Dapodik Anda')
                            ->schema([
                                FileUpload::make('file')
                                    ->label('File Excel Dapodik')
                                    ->directory('imports')
                                    ->acceptedFileTypes([
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'application/vnd.ms-excel',
                                        'text/csv'
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if (!$state) return;
                                        
                                        // Baca file untuk preview
                                        $tempPath = tempnam(sys_get_temp_dir(), 'preview_');
                                        file_put_contents($tempPath, Storage::get($state));
                                        
                                        try {
                                            $headerRowNumber = 5;
                                            $tempRows = SimpleExcelReader::create($tempPath)->noHeaderRow()->getRows();
                                            foreach ($tempRows as $index => $row) {
                                                $rowString = implode(' ', array_values($row));
                                                if (stripos($rowString, 'NISN') !== false && stripos($rowString, 'Nama') !== false) {
                                                    $headerRowNumber = $index + 1;
                                                    break;
                                                }
                                            }

                                            $rows = SimpleExcelReader::create($tempPath)
                                                ->headerOnRow($headerRowNumber - 1)
                                                ->getRows()
                                                ->skip(1);

                                            $previewData = [];
                                            foreach ($rows as $row) {
                                                $nisn = (string)($row['NISN'] ?? '');
                                                if (empty($nisn) || stripos($nisn, 'NISN') !== false) continue;

                                                $exists = Student::where('nisn', $nisn)->exists();

                                                $previewData[] = [
                                                    'should_import' => !$exists,
                                                    'nisn' => $nisn,
                                                    'full_name' => $row['Nama'] ?? 'N/A',
                                                    'is_exists' => $exists,
                                                    'raw_data' => $row,
                                                ];
                                            }
                                            
                                            $set('preview_data', $previewData);
                                        } catch (\Exception $e) {
                                            // Error silent
                                        } finally {
                                            if (file_exists($tempPath)) unlink($tempPath);
                                        }
                                    }),
                            ]),
                        
                        Step::make('Review Data')
                            ->description('Pilih data yang ingin dimasukkan')
                            ->schema([
                                Repeater::make('preview_data')
                                    ->label('Daftar Calon Santri')
                                    ->schema([
                                        Section::make()
                                            ->schema([
                                                Checkbox::make('should_import')
                                                    ->label('Pilih')
                                                    ->columnSpan(1),
                                                TextInput::make('nisn')
                                                    ->label('NISN')
                                                    ->disabled()
                                                    ->columnSpan(2),
                                                TextInput::make('full_name')
                                                    ->label('Nama Lengkap')
                                                    ->disabled()
                                                    ->columnSpan(3),
                                                Toggle::make('is_exists')
                                                    ->label('Status')
                                                    ->onIcon('heroicon-m-exclamation-triangle')
                                                    ->offIcon('heroicon-m-check')
                                                    ->onColor('danger')
                                                    ->offColor('success')
                                                    ->disabled()
                                                    ->columnSpan(2),
                                            ])
                                            ->columns(8)
                                            ->compact()
                                    ])
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->itemLabel(fn (array $state): ?string => $state['full_name'] ?? null),
                            ]),
                    ])->persistStepInQueryString('import-step'),
                ])
                ->action(function (array $data) {
                    $previewData = $data['preview_data'] ?? [];
                    $toImport = array_filter($previewData, fn($item) => $item['should_import'] ?? false);

                    if (empty($toImport)) {
                        Notification::make()->warning()->title('Tidak ada data yang dipilih')->send();
                        return;
                    }

                    $successCount = 0;
                    $failedCount = 0;

                    foreach ($toImport as $item) {
                        try {
                            $row = $item['raw_data'];
                            $nisn = $item['nisn'];

                            $genderRaw = $row['JK'] ?? null;
                            $gender = Gender::MALE;
                            if (stripos((string)$genderRaw, 'P') !== false) { $gender = Gender::FEMALE; }

                            $dobRaw = $row['Tanggal Lahir'] ?? null;
                            $dob = null;
                            if ($dobRaw) {
                                try { $dob = \Carbon\Carbon::parse($dobRaw)->format('Y-m-d'); } catch (\Exception $e) {}
                            }

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
                                'sibling_count'    => $clean($row['Jml. Saudara Kandung']) ?? null,
                                'status'           => StudentStatus::ACTIVE,
                                'residency_status' => ResidencyStatus::TIDAK_MUKIM,
                            ]);

                            $successCount++;
                        } catch (\Exception $e) {
                            $failedCount++;
                        }
                    }

                    if (isset($data['file'])) { Storage::delete($data['file']); }

                    Notification::make()
                        ->success()
                        ->title('Import Selesai')
                        ->body("{$successCount} data berhasil diimport.")
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
