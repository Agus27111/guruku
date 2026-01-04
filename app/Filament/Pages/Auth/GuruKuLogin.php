<?php

namespace App\Filament\Pages\Auth;

use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use Filament\Auth\Pages\Login as PagesLogin;
use Filament\Pages\Auth\Login;

class GuruKuLogin extends PagesLogin
{
    use HasCustomLayout;

    public function getHeading(): string
    {
        return 'Login GuruKu';
    }
}
