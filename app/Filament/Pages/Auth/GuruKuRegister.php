<?php

namespace App\Filament\Pages\Auth;

use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use Filament\Auth\Pages\Register;
use App\Models\School;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class GuruKuRegister extends Register
{
    use HasCustomLayout;

    public function form(Schema $schema): Schema
    {
        return parent::form($schema)
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(100),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email'),

                TextInput::make('school_name')
                    ->label('Nama Sekolah / Workspace')
                    ->required()
                    ->maxLength(120),

                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function handleRegistration(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // ✅ buat 1 school untuk user baru
        $school = School::create([
            'name' => $data['school_name'],
        ]);

        // ✅ attach membership
        $user->schools()->attach($school->id);

        return $user;
    }
}
