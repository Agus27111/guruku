<?php

namespace App\Filament\Resources\LearningJournals\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LearningJournalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembelajaran')
                    ->schema([
                        DatePicker::make('date')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),

                        Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->relationship('subject', 'name',  fn($query) =>
                            $query->where('user_id', auth()->id()))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Mata Pelajaran')
                                    ->placeholder('Contoh: Matematika')
                                    ->required()
                                    ->maxLength(100),

                                TextInput::make('code')
                                    ->label('Kode Mata Pelajaran')
                                    ->placeholder('Contoh: MATH101')
                                    ->maxLength(20),

                                Hidden::make('user_id')
                                    ->default(fn() => auth()->id())
                                    ->required(),
                            ])
                            ->createOptionAction(
                                fn($action) => $action
                                    ->label('Tambah Mata Pelajaran')
                                    ->modalHeading('Tambah Mata Pelajaran Baru')
                                    ->modalSubmitActionLabel('Simpan')
                            ),

                        Select::make('classroom_id')
                            ->label('Kelas')
                            ->relationship(
                                'classroom',
                                'name',
                                fn($query) =>
                                $query->where('user_id', auth()->id())
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        CheckboxList::make('teaching_hours')
                            ->label('Jam Pelajaran (Ceklis yang sesuai)')
                            ->options([
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',
                                '5' => '5',
                                '6' => '6',
                                '7' => '7',
                                '8' => '8',
                                '9' => '9',
                            ])
                            ->columns(3)
                            ->required(),
                    ])->columns(2),

                Section::make('Konten Jurnal')
                    ->schema([
                        TextInput::make('topic')
                            ->label('Topik / Materi')
                            ->required(),

                        Textarea::make('activity')
                            ->label('Kegiatan')
                            ->required(),

                        Textarea::make('note')
                            ->label('Keterangan'),

                        FileUpload::make('photo')
                            ->label('Foto Dokumentasi')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->maxSize(5120) // dalam KB, 5MB
                            ->directory('journal-photos')
                            ->visibility('public'),
                    ]),
            ]);
    }
}
