<?php

namespace App\Filament\Resources\Tahsins\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TahsinForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Progres Tahsin')
                    ->description('Catat perkembangan bacaan metode (Ummi, Qiraati, dll) santri.')
                    ->schema([
                        // Pilih Santri
                        Select::make('student_id')
                            ->relationship('student', 'name')
                            ->label('Nama Santri')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // Tanggal Baca
                        DatePicker::make('read_at')
                            ->label('Tanggal Baca')
                            ->default(now())
                            ->required(),

                        // Metode / Jenis Buku
                        Select::make('type')
                            ->label('Metode / Buku')
                            ->options(function () {
                                // Mengambil data yang sudah pernah diinput agar muncul di pilihan
                                $existing = \App\Models\Tahsin::distinct()->pluck('type', 'type')->toArray();

                                $defaults = [
                                    'Ummi' => 'Ummi',
                                    'Iqro' => 'Iqro',
                                    'Qiraati' => 'Qiraati',
                                    'Bayyinah' => 'Bayyinah',
                                    'Ali' => 'Ali',
                                    'Lainnya' => 'Lainnya',
                                ];

                                return array_merge($defaults, $existing);
                            })
                            ->searchable()
                            ->required(),

                        // Jilid dan Halaman berdampingan
                        Grid::make(2)
                            ->schema([
                                TextInput::make('volume')
                                    ->label('Jilid')
                                    ->placeholder('Contoh: 3'),
                                TextInput::make('page')
                                    ->label('Halaman')
                                    ->placeholder('Contoh: 15'),
                            ]),

                        // Penilaian
                        Select::make('predicate')
                            ->label('Kualitas Bacaan')
                            ->options([
                                'fluent' => 'Lancar',
                                'Struggling' => 'Kurang Lancar / Mengulang',
                            ])
                            ->required(),

                        // Catatan Guru
                        Textarea::make('note')
                            ->label('Catatan Tambahan')
                            ->placeholder('Tulis evaluasi santri di sini...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2) // Isi di dalam section dibagi 2 kolom
                    ->columnSpanFull(), // Section memenuhi lebar layar
            ]);
    }
}
