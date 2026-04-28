<?php

namespace App\Filament\Guardian\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Facades\Filament;

class AccountPendingApproval extends Page
{
    protected static BackedEnum|string|null $navigationIcon = null;
    
    // Hide from sidebar
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.guardian.pages.account-pending-approval';

    protected static ?string $title = 'Akun Menunggu Verifikasi';

    public function getLayout(): string
    {
        return 'filament-panels::components.layout.simple';
    }

    public function logout()
    {
        Filament::auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->to(Filament::getLoginUrl());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('logout')
                ->label('Keluar')
                ->color('gray')
                ->action(function () {
                    Filament::auth()->logout();
                    return redirect()->to(Filament::getLoginUrl());
                }),
        ];
    }
}
