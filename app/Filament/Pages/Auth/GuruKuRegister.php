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
use Filament\Forms\Components\Radio;

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
                    ->unique(\App\Models\User::class, 'email'),

                Radio::make('school_mode')
                    ->label('Sekolah')
                    ->options([
                        'create' => 'Buat sekolah baru',
                        'join'   => 'Gabung sekolah (pakai kode undangan)',
                    ])
                    ->default('create')
                    ->live()
                    ->required(),

                TextInput::make('school_name')
                    ->label('Nama Sekolah / Workspace')
                    ->required(fn($get) => $get('school_mode') === 'create')
                    ->visible(fn($get) => $get('school_mode') === 'create')
                    ->maxLength(120),

                TextInput::make('invite_code')
                    ->label('Kode Undangan')
                    ->required(fn($get) => $get('school_mode') === 'join')
                    ->visible(fn($get) => $get('school_mode') === 'join')
                    ->exists(table: 'schools', column: 'invite_code') // Pastikan kode ada di DB
                    ->validationMessages([
                        'exists' => 'Kode undangan tidak ditemukan.',
                    ]),

                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function handleRegistration(array $data): User
    {
        // 1. Buat User
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // Jangan di-Hash::make karena Model sudah pakai casting 'hashed'
        ]);

        // 2. Logika Sekolah
        if ($data['school_mode'] === 'create') {
            // Buat sekolah baru
            $school = School::create([
                'name' => $data['school_name'],
                // slug otomatis terisi via Booted Event di Model School
            ]);

            $user->schools()->attach($school);
        } else {
            // Logika Gabung Sekolah (Join)
            $school = School::where('invite_code', $data['invite_code'])->first();

            if ($school) {
                $user->schools()->attach($school);
            } else {
                // Opsional: Jika kode salah, buatkan sekolah default agar tidak error 
                // atau tambahkan validasi custom pada form
                throw new \Exception('Kode undangan tidak valid.');
            }
        }

        return $user;
    }
}
