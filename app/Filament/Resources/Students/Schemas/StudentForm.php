<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Hidden;

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
                            ->required()
                            ->createOptionForm([
                                Hidden::make('user_id')
                                    ->default(fn() => auth()->id())
                                    ->required(),

                                Hidden::make('school_id')
                                    ->default(auth()->user()->school_id),

                                TextInput::make('name')
                                    ->label('Nama Kelas')
                                    ->placeholder('Contoh: 1A')
                                    ->required()
                                    ->maxLength(50),
                            ])
                            ->createOptionAction(
                                fn(Action $action) => $action
                                    ->label('Tambah Kelas')
                                    ->modalHeading('Tambah Kelas Baru')
                                    ->modalSubmitActionLabel('Simpan')
                            ),

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

                        FileUpload::make('photo')
                            ->label('Foto Siswa')
                            ->image()
                            ->avatar() // Membuat preview berbentuk lingkaran (opsional, bagus untuk profil)
                            ->imageEditor() // Memungkinkan guru crop foto agar pas
                            ->maxSize(5120) // dalam KB, 5MB
                            ->directory('student-photos')
                            ->visibility('public'),
                    ])
            ]);
    }
}
