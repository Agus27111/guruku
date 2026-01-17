<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Login')
                    ->description('Data autentikasi pengguna')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At'),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->label('Password'),
                    ]),

                Section::make('Subscription & Sekolah')
                    ->description('Pengaturan status PRO dan keterkaitan sekolah')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_pro')
                            ->label('Status Langganan PRO')
                            ->required()
                            ->columnSpanFull(),
                        DatePicker::make('pro_expired_at')
                            ->label('Masa Berlaku PRO'),
                        Select::make('school_id')
                            ->relationship('school', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Terhubung ke Sekolah'),
                        Toggle::make('is_pro')
                            ->label('Status Langganan PRO')
                            ->reactive() // Membuat form bereaksi saat ini diklik
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    // Jika PRO dinyalakan, otomatis set expired 1 tahun lagi
                                    $set('pro_expired_at', now()->addYear()->format('Y-m-d'));
                                } else {
                                    // Jika dimatikan, hapus tanggal expired
                                    $set('pro_expired_at', null);
                                }
                            })
                    ]),

                Section::make('Fitur Jurnal (Feature Flags)')
                    ->description('Aktifkan atau matikan menu jurnal secara spesifik')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_tahfidz_enabled')
                            ->label('Jurnal Tahfidz')
                            ->required(),
                        Toggle::make('is_tahsin_enabled')
                            ->label('Jurnal Tahsin')
                            ->required(),
                        Toggle::make('is_read_enabled')
                            ->label('Jurnal Baca')
                            ->required(),
                        Toggle::make('is_studentDevelopment_enabled')
                            ->label('Pengembangan Siswa')
                            ->required(),
                        Toggle::make('is_assessment_enabled')
                            ->label('Jurnal Penilaian')
                            ->required(),
                    ]),

                Section::make('Otorisasi')
                    ->description('Tentukan peran user dalam sistem')
                    ->schema([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Roles / Peran'),
                    ]),
            ]);
    }
}
