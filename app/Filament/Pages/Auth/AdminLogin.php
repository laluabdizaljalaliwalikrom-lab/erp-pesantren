<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\View\View;

class AdminLogin extends BaseLogin
{
    /**
     * @return string
     */
    public function getLayout(): string
    {
        return 'filament.admin.layouts.login';
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent()
                    ->placeholder('Alamat Email')
                    ->label(null),
                $this->getPasswordFormComponent()
                    ->placeholder('Kata Sandi')
                    ->label(null),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }
}
