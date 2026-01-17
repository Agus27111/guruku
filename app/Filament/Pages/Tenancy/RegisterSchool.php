<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\School;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;

class RegisterSchool extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Buat Sekolah';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nama Sekolah')
                ->required()
                ->maxLength(100),
        ]);
    }

    protected function handleRegistration(array $data): School
    {
        $school = School::create($data);

        $user = auth()->user();
        $school->members()->attach($user);


        return $school;
    }

    public function mount(): void
    {
        // ✅ kalau user sudah punya school, jangan boleh buat baru
        if (auth()->user()->schools()->exists()) {
            redirect(\Filament\Facades\Filament::getCurrentPanel()->getUrl());
        }
        parent::mount();
    }

    public static function canView(): bool
    {
        // Jika user sudah punya minimal 1 sekolah, jangan tampilkan halaman ini
        return auth()->user()->schools()->count() === 0;
    }
}
