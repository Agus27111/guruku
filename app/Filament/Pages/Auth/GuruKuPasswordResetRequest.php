<?php

namespace App\Filament\Pages\Auth;

use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;

class GuruKuPasswordResetRequest extends RequestPasswordReset
{
    use HasCustomLayout;
}
