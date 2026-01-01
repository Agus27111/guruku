<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Profil Siswa')
                    ->description('Pastikan data NISN sesuai dengan data Dapodik.')
                    ->schema([
                        Select::make('classroom_id')
                            ->label('Kelas')
                            ->relationship(
                                'classroom',
                                'name',
                                fn($query) => $query->where('user_id', auth()->id())
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->label('Nama Lengkap Siswa')
                            ->placeholder('Contoh: Ahmad Subardjo')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('nisn')
                            ->label('NISN')
                            ->placeholder('Nomor Induk Siswa Nasional')
                            ->numeric()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10),
                    ])
            ]);
    }
}
