<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Pages\Auth;

use App\Models\Student;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class GuardianRegister extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getNikFormComponent(),
                $this->getGenderFormComponent(),
                $this->getPhoneFormComponent(),
                $this->getAddressFormComponent(),
                $this->getNisSantriFormComponent(),
                $this->getNamaSantriFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getNikFormComponent()
    {
        return TextInput::make('nik')
            ->label('NIK Wali')
            ->placeholder('Masukkan 16 digit NIK')
            ->required()
            ->numeric()
            ->length(16)
            ->unique('guardians', 'nik');
    }

    protected function getPhoneFormComponent()
    {
        return TextInput::make('phone_number')
            ->label('Nomor WhatsApp')
            ->placeholder('Contoh: 08123456789')
            ->required()
            ->tel()
            ->unique('guardians', 'phone_number');
    }

    protected function getGenderFormComponent()
    {
        return Select::make('gender')
            ->label('Jenis Kelamin')
            ->options([
                'male' => 'Laki-laki',
                'female' => 'Perempuan',
            ])
            ->required()
            ->native(false);
    }

    protected function getAddressFormComponent()
    {
        return Textarea::make('address')
            ->label('Alamat Lengkap')
            ->placeholder('Masukkan alamat lengkap sesuai KTP')
            ->required()
            ->rows(3);
    }

    protected function getNisSantriFormComponent()
    {
        return TextInput::make('nis_santri')
            ->label('NIS Santri')
            ->placeholder('Nomor Induk Santri')
            ->required()
            ->exists('students', 'nis')
            ->suffixAction(
                Action::make('search')
                    ->icon('heroicon-m-magnifying-glass')
                    ->color('primary')
                    ->tooltip('Cari data santri')
                    ->action(function (Set $set, ?string $state) {
                        if (!$state) {
                            Notification::make()
                                ->title('NIS harus diisi')
                                ->warning()
                                ->send();
                            return;
                        }

                        $student = Student::where('nis', $state)->first();
                        
                        if ($student) {
                            if ($student->guardian_id) {
                                Notification::make()
                                    ->title('Santri sudah terverifikasi')
                                    ->body('Santri ini sudah memiliki wali yang terdaftar.')
                                    ->danger()
                                    ->send();
                                $set('nama_santri', null);
                                return;
                            }

                            $set('nama_santri', $student->full_name);
                            
                            Notification::make()
                                ->title('Santri ditemukan!')
                                ->body("Data ditemukan atas nama: {$student->full_name}")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Data tidak ditemukan')
                                ->body('Periksa kembali NIS yang Anda masukkan.')
                                ->danger()
                                ->send();
                            $set('nama_santri', null);
                        }
                    })
            );
    }

    protected function getNamaSantriFormComponent()
    {
        return TextInput::make('nama_santri')
            ->label('Nama Lengkap Santri')
            ->placeholder('Nama otomatis terisi...')
            ->required()
            ->readOnly()
            ->helperText('Nama akan muncul otomatis setelah NIS diinput dengan benar.');
    }

    protected function handleRegistration(array $data): Model
    {
        // 1. Strict Validation for Student Linking (Application stage)
        $student = Student::where('nis', $data['nis_santri'])->first();

        if (!$student) {
            throw ValidationException::withMessages([
                'data.nis_santri' => 'Data santri tidak ditemukan.',
            ]);
        }

        if ($student->guardian_id) {
            throw ValidationException::withMessages([
                'data.nis_santri' => 'Santri ini sudah memiliki wali terverifikasi.',
            ]);
        }

        // Exact match check (case-insensitive for better UX but strict enough)
        if (trim(strtolower($student->full_name)) !== trim(strtolower($data['nama_santri']))) {
            throw ValidationException::withMessages([
                'data.nama_santri' => 'Nama santri tidak cocok dengan NIS yang dimasukkan.',
            ]);
        }

        // 2. Create the Guardian record (Unapproved)
        /** @var Model $guardian */
        $guardian = $this->getUserModel()::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'nik' => $data['nik'],
            'gender' => $data['gender'],
            'phone_number' => $data['phone_number'],
            'address' => $data['address'],
            'is_approved' => false, // Set to false by default
            'registration_nis_request' => $data['nis_santri'], // Save NIS for admin reference
        ]);

        // Note: We NO LONGER update the student->guardian_id here.
        // It will be done by the Admin upon approval.

        return $guardian;
    }
}
