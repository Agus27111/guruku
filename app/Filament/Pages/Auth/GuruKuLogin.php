<?php

namespace App\Filament\Pages\Auth;

use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use Filament\Auth\Pages\Login;
use Filament\Pages\Auth\Login as BaseLogin;

class GuruKuLogin extends Login
{
    use HasCustomLayout;

    public function getHeading(): string
    {
        return 'Login GuruKu';
    }
}
