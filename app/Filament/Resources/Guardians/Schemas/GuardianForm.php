<?php

declare(strict_types=1);

namespace App\Filament\Resources\Guardians\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GuardianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->prefixIcon('heroicon-m-user'),
                        TextInput::make('nik')
                            ->numeric()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-m-identification'),
                        TextInput::make('phone_number')
                            ->tel()
                            ->required()
                            ->prefix('+62')
                            ->prefixIcon('heroicon-m-phone'),
                        TextInput::make('email')
                            ->email()
                            ->prefixIcon('heroicon-m-envelope'),
                        Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->native(false),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Facades\Hash::make($state))
                            ->same('password_confirmation')
                            ->validationMessages([
                                'same' => 'Password confirmation does not match.',
                            ]),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->dehydrated(false)
                            ->revealable()
                            ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                        Textarea::make('address')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
