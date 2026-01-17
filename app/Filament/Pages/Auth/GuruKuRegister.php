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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

                TextInput::make('school_name')
                    ->label('Nama Sekolah')
                    ->placeholder('Contoh: SD Negeri 1 Indramayu') // Sekarang ini akan bekerja
                    ->helperText('Nama sekolah akan muncul di setiap laporan jurnal Anda.')
                    ->required()
                    ->maxLength(255),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // 1. Buat User (Guru) dengan default setting personal
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                // Logika Trial PRO 1 Bulan
                'is_pro' => true,
                'pro_expired_at' => now()->addDays(7), //gratis 7 hari setelah daftar

                // Aktifkan semua fitur agar mereka bisa mencoba (Trial Experience)
                'is_tahfidz_enabled' => true,
                'is_tahsin_enabled' => true,
                'is_read_enabled' => true,
                'is_studentDevelopment_enabled' => true,
                'is_assessment_enabled' => true,
            ]);

            $user->assignRole(\Spatie\Permission\Models\Role::firstOrCreate(['name' => 'panel_user']));

            // 2. Buat Sekolah sebagai "Workspace" pribadi guru
            $school = School::create([
                'name' => $data['school_name'],
                // slug & invite_code otomatis dibuat di Booted Event Model School
            ]);

            // 3. Hubungkan ke User melalui pivot agar relasi tetap terjaga
            $user->schools()->attach($school);

            return $user;
        });
    }

    protected function getMessages(): array
    {
        return [
            'data.password.min' => 'Password terlalu pendek, minimal harus 8 karakter.',
            'data.email.unique' => 'Email ini sudah terdaftar, silakan gunakan email lain.',
            'data.name.required' => 'Nama lengkap wajib diisi.',
        ];
    }
}
