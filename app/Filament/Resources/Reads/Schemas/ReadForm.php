<?php

namespace App\Filament\Resources\Reads\Schemas;

use App\Helpers\QuranHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class ReadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Jurnal Baca Al-Quran (Bin-Nazhar)')
                    ->description('Catat progres bacaan Al-Quran santri secara rutin.')
                    ->schema([
                        // 1. Pilih Santri
                        Select::make('student_id')
                            ->relationship('student', 'name')
                            ->label('Nama Santri')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // 2. Tanggal Baca
                        DatePicker::make('read_at')
                            ->label('Tanggal Baca')
                            ->default(now())
                            ->required(),

                        // 3. Nama Metode Baca 
                        TextInput::make('type')
                            ->label('Nama Metode Baca')
                            ->placeholder('Contoh: Ahoy dll')
                            ->required(),

                        // 4. Jilid/Juz dan Halaman berdampingan
                        ComponentsGrid::make(2)
                            ->schema([
                                TextInput::make('volume')
                                    ->label('Juz / Jilid')
                                    ->placeholder('Contoh: 30')
                                    ->numeric(),
                                TextInput::make('page')
                                    ->label('Halaman')
                                    ->placeholder('Contoh: 602'),

                            ]),

                        // 5. Penilaian
                        Select::make('predicate')
                            ->label('Kualitas Bacaan')
                            ->options([
                                'fluent' => 'Lancar',
                                'Struggling' => 'Kurang Lancar',
                            ])
                            ->required(),

                        // 6. Catatan Guru
                        Textarea::make('note')
                            ->label('Catatan Tambahan')
                            ->placeholder('Tulis evaluasi bacaan di sini...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
