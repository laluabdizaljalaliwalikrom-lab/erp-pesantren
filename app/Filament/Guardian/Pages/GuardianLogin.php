<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Pages;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Support\HtmlString;

class GuardianLogin extends BaseLogin
{
    // Default actions will now only include the Sign In button.
    // The Sign Up link has been moved to the AUTH_LOGIN_FORM_AFTER render hook
    // to ensure it is positioned vertically below the form actions.

    public function hasRegistrationLink(): bool
    {
        return false;
    }
}
