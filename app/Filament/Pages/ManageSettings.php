<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\AppSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Storage;

class ManageSettings extends Page implements HasSchemas
{
    use HasPageShield;
    use InteractsWithSchemas;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|null|\UnitEnum $navigationGroup = 'Pengaturan';

    protected static int | null $navigationSort = 1;

    protected static ?string $title = 'Application Settings';

    protected static ?string $navigationLabel = 'App Settings';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->getSchema('form')->fill(AppSetting::instance()->toArray());
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Informasi Umum')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Data Pesantren')
                                    ->description('Informasi utama lembaga Anda.')
                                    ->schema([
                                        TextInput::make('pesantren_name')
                                            ->label('Nama Pesantren')
                                            ->required()
                                            ->placeholder('Contoh: Pondok Pesantren Al-Hikmah'),

                                        TextInput::make('leader_name')
                                            ->label('Nama Pimpinan / Pengasuh')
                                            ->placeholder('Contoh: KH. Ahmad Dahlan'),

                                        TextInput::make('footer_text')
                                            ->label('Teks Footer')
                                            ->placeholder('Contoh: © 2024 ERP Pesantren. All rights reserved.'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Kontak & Alamat')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make('Informasi Kontak')
                                    ->schema([
                                        TextInput::make('email')
                                            ->label('Email Resmi')
                                            ->email()
                                            ->placeholder('admin@pesantren.com'),

                                        TextInput::make('phone')
                                            ->label('Nomor Telepon / WhatsApp')
                                            ->tel()
                                            ->placeholder('08123456789'),

                                        TextInput::make('address')
                                            ->label('Alamat Lengkap')
                                            ->placeholder('Jl. Raya Pesantren No. 1...'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Branding')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Logo & Favicon')
                                    ->description('Sesuaikan identitas visual aplikasi.')
                                    ->schema([
                                        FileUpload::make('logo_path')
                                            ->label('Logo Aplikasi')
                                            ->disk('public')
                                            ->image()
                                            ->directory('app-settings')
                                            ->visibility('public')
                                            ->maxSize(2048)
                                            ->imagePreviewHeight('150'),

                                        FileUpload::make('favicon_path')
                                            ->label('Favicon')
                                            ->disk('public')
                                            ->image()
                                            ->directory('app-settings')
                                            ->visibility('public')
                                            ->maxSize(512),

                                        ColorPicker::make('primary_color')
                                            ->label('Warna Utama (Primary Color)')
                                            ->default('#008000')
                                            ->required(),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    EmbeddedSchema::make('form'),
                ])
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make($this->getFormActions())
                        ->alignment(Alignment::Start),
                ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->getSchema('form')->getState();

            AppSetting::instance()->update($data);
            app(\App\Services\SettingsService::class)->refresh();

            Notification::make()
                ->success()
                ->title('Settings saved successfully!')
                ->send();
        } catch (Halt $exception) {
            return;
        }
    }
}
