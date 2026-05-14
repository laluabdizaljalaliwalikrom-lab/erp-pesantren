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
                                    ->afterStateUpdated(function ($state, $set, $component) {
                                        if (!$state) return;
                                        
                                        try {
                                            // Cek apakah $state sudah berupa objek file atau masih nama file
                                            $file = ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) 
                                                ? $state 
                                                : ($component->getUploadedFiles()[$state] ?? null);

                                            if (!$file) return;

                                            $extension = $file->getClientOriginalExtension();
                                            $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'preview_' . uniqid() . '.' . $extension;
                                            
                                            $fileContent = $file->get();
                                            if (!$fileContent) return;
                                            
                                            file_put_contents($tempPath, $fileContent);
                                        } catch (\Exception $e) {
                                            return;
                                        }
                                        
                                        try {
                                            $reader = SimpleExcelReader::create($tempPath);
                                            
                                            // 1. Cari baris judul (header)
                                            $headerRowNumber = 1; 
                                            $allRows = $reader->noHeaderRow()->getRows()->take(20);
                                            foreach ($allRows as $index => $row) {
                                                $rowString = strtoupper(implode(' ', array_values($row)));
                                                if (str_contains($rowString, 'NISN') && (str_contains($rowString, 'NAMA') || str_contains($rowString, 'PESERTA'))) {
                                                    $headerRowNumber = $index + 1;
                                                    break;
                                                }
                                            }

                                            // 2. Baca ulang data dari baris judul yang ditemukan
                                            $rows = SimpleExcelReader::create($tempPath)
                                                ->headerOnRow($headerRowNumber - 1)
                                                ->getRows();

                                            $previewData = [];
                                            foreach ($rows as $row) {
                                                $cleanRow = [];
                                                foreach($row as $key => $val) { $cleanRow[trim(strtoupper($key))] = $val; }

                                                $nisn = trim((string)($cleanRow['NISN'] ?? ''));
                                                $nis  = trim((string)($cleanRow['NIPD'] ?? $cleanRow['NIS'] ?? ''));
                                                $nama = $cleanRow['NAMA'] ?? $cleanRow['NAMA PESERTA DIDIK'] ?? null;

                                                if (empty($nisn) || $nisn === 'NISN') continue;

                                                $activeStudent = Student::where('nisn', $nisn)->first();
                                                if (!$activeStudent && !empty($nis)) {
                                                    $activeStudent = Student::where('nis', $nis)->first();
                                                }

                                                $trashedStudent = null;
                                                if (!$activeStudent) {
                                                    $trashedStudent = Student::onlyTrashed()->where('nisn', $nisn)->first();
                                                    if (!$trashedStudent && !empty($nis)) {
                                                        $trashedStudent = Student::onlyTrashed()->where('nis', $nis)->first();
                                                    }
                                                }

                                                $status = 'baru';
                                                $shouldImport = true;
                                                
                                                if ($activeStudent) {
                                                    $status = 'aktif';
                                                    $shouldImport = false; // Admin pilih sendiri mau update atau tidak
                                                } elseif ($trashedStudent) {
                                                    $status = 'terhapus';
                                                    $shouldImport = true; // Anggap baru/restore otomatis
                                                }

                                                $previewData[] = [
                                                    'should_import' => $shouldImport,
                                                    'nisn' => $nisn,
                                                    'full_name' => $nama ?? 'N/A',
                                                    'status_data' => $status,
                                                    'raw_data' => $row,
                                                ];
                                            }
                                            
                                            $set('preview_data', $previewData);
                                            
                                            if (empty($previewData)) {
                                                Notification::make()->warning()->title('Data tidak ditemukan')->body('Sistem tidak menemukan kolom NISN dan Nama di file tersebut.')->send();
                                            }

                                        } catch (\Exception $e) {
                                            Notification::make()->danger()->title('Gagal memproses Excel')->body($e->getMessage())->send();
                                        } finally {
                                            // Pastikan reader dilepas sebelum dihapus
                                            unset($reader);
                                            unset($rows);
                                            if (file_exists($tempPath)) @unlink($tempPath);
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
                                                    ->dehydrated()
                                                    ->hint(fn ($state, $get) => match($get('status_data')) {
                                                        'aktif' => '⚠️ Sudah Terdaftar',
                                                        'terhapus' => '♻️ Pernah Dihapus',
                                                        default => null,
                                                    })
                                                    ->hintColor(fn ($get) => $get('status_data') === 'terhapus' ? 'info' : 'warning')
                                                    ->columnSpan(2),
                                                TextInput::make('full_name')
                                                    ->label('Nama Lengkap')
                                                    ->disabled()
                                                    ->columnSpan(3),
                                                TextInput::make('status_data')
                                                    ->label('Status')
                                                    ->formatStateUsing(fn ($state) => match($state) {
                                                        'aktif' => '⚠️ Sudah Ada (Aktif)',
                                                        'terhapus' => '♻️ Pernah Dihapus',
                                                        default => '✅ Data Baru',
                                                    })
                                                    ->extraInputAttributes(fn ($state) => [
                                                        'style' => match($state) {
                                                            'aktif' => 'color: orange; font-weight: bold',
                                                            'terhapus' => 'color: blue; font-weight: bold',
                                                            default => 'color: green; font-weight: bold',
                                                        }
                                                    ])
                                                    ->disabled()
                                                    ->columnSpan(2),
                                                \Filament\Forms\Components\Hidden::make('raw_data'), // Simpan data asli di sini
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
                    $errors = [];

                    foreach ($toImport as $item) {
                        try {
                            $raw = $item['raw_data'];
                            // Bersihkan kunci agar mudah diakses (uppercase & trim)
                            $row = [];
                            foreach($raw as $k => $v) { $row[trim(strtoupper($k))] = $v; }
                            
                            $nisn = $item['nisn'];
                            $nis  = trim((string)($row['NIPD'] ?? $row['NIS'] ?? ''));

                            // Cari data yang sudah ada (termasuk yang dihapus)
                            $student = Student::withTrashed()->where('nisn', $nisn)->first();
                            if (!$student && !empty($nis)) {
                                $student = Student::withTrashed()->where('nis', $nis)->first();
                            }

                            $genderRaw = $row['JK'] ?? $row['JENIS KELAMIN'] ?? null;
                            $gender = Gender::MALE;
                            if ($genderRaw && stripos((string)$genderRaw, 'P') !== false) { $gender = Gender::FEMALE; }

                            $dobRaw = $row['TANGGAL LAHIR'] ?? null;
                            $dob = null;
                            if ($dobRaw) {
                                try { $dob = \Carbon\Carbon::parse($dobRaw)->format('Y-m-d'); } catch (\Exception $e) {}
                            }

                            $clean = fn($val) => (empty(trim((string)$val)) ? null : trim((string)$val));

                            $studentData = [
                                'nisn'             => $nisn,
                                'full_name'        => $clean($row['NAMA'] ?? $row['NAMA PESERTA DIDIK'] ?? 'Tanpa Nama'),
                                'nis'              => $clean($row['NIPD'] ?? $row['NIS'] ?? null),
                                'nik'              => $clean($row['NIK'] ?? null),
                                'no_kk'            => $clean($row['NO KK'] ?? null),
                                'birth_place'      => $clean($row['TEMPAT LAHIR'] ?? null),
                                'birth_date'       => $dob,
                                'gender'           => $gender,
                                'address'          => $clean($row['ALAMAT'] ?? null),
                                'rt'               => $clean($row['RT'] ?? null),
                                'rw'               => $clean($row['RW'] ?? null),
                                'dusun'            => $clean($row['DUSUN'] ?? null),
                                'village'          => $clean($row['KELURAHAN'] ?? null),
                                'district'         => $clean($row['KECAMATAN'] ?? null),
                                'father_name'      => $clean($row['DATA AYAH'] ?? $row['NAMA AYAH'] ?? null),
                                'mother_name'      => $clean($row['DATA IBU'] ?? $row['NAMA IBU'] ?? null),
                                'hp'               => $clean($row['HP'] ?? $row['TELEPON'] ?? null),
                                'email'            => $clean($row['E-MAIL'] ?? null),
                                'previous_school'  => $clean($row['SEKOLAH ASAL'] ?? null),
                                'sibling_position' => $clean($row['ANAK KE-BERAPA'] ?? null),
                                'sibling_count'    => $clean($row['JML. SAUDARA KANDUNG'] ?? null),
                                'status'           => StudentStatus::ACTIVE,
                                'residency_status' => ResidencyStatus::TIDAK_MUKIM,
                            ];

                            if ($student) {
                                // Pulihkan jika terhapus
                                if ($student->trashed()) {
                                    $student->restore();
                                }
                                // Update data lama
                                $student->update($studentData);
                            } else {
                                // Buat data baru
                                Student::create($studentData);
                            }

                            $successCount++;
                        } catch (\Exception $e) {
                            $failedCount++;
                            $errors[] = $e->getMessage();
                        }
                    }

                    if (isset($data['file'])) { Storage::delete($data['file']); }

                    Notification::make()
                        ->success()
                        ->title('Import Selesai')
                        ->body("{$successCount} data berhasil diimport.")
                        ->send();

                    if ($failedCount > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Beberapa Data Gagal')
                            ->body("{$failedCount} data gagal diimport. Contoh error: " . ($errors[0] ?? 'Unknown error'))
                            ->send();
                    }
                }),

            CreateAction::make(),
        ];
    }
}
