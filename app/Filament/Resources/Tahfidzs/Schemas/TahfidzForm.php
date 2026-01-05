<?php

namespace App\Filament\Resources\Tahfidzs\Schemas;

use App\Helpers\QuranHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Set;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Components\Utilities\Set as UtilitiesSet;
use Filament\Schemas\Schema; // Pastikan import ini ada

class TahfidzForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Progres Tahfidz')
                    ->schema([
                        Select::make('student_id')
                            ->label('Pilih Siswa')
                            ->relationship(
                                'student',
                                'name',
                                fn($query) => $query
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('surah')
                            ->label('Nama Surah')
                            ->options(array_combine(
                                array_keys(QuranHelper::getSurahs()),
                                array_keys(QuranHelper::getSurahs())
                            ))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, UtilitiesSet $set) {
                                if ($state) {
                                    $surahs = QuranHelper::getSurahs();
                                    if (isset($surahs[$state])) {
                                        $data = $surahs[$state];
                                        $set('juz', $data['juz']);
                                    }
                                }
                            })
                            ->required(),

                        TextInput::make('juz')
                            ->numeric()
                            ->required()
                            ->label('Juz'),

                        ComponentsGrid::make(2)
                            ->schema([
                                TextInput::make('start_verse')
                                    ->label('Ayat Mulai')
                                    ->numeric(),
                                TextInput::make('end_verse')
                                    ->label('Ayat Selesai')
                                    ->numeric(),
                            ]),

                        Select::make('predicate')
                            ->label('Predikat')
                            ->options([
                                'fluent' => 'Lancar',
                                'Struggling' => 'Kurang Lancar',
                            ])->required(),

                        Textarea::make('note')
                            ->label('Catatan/Keterangan')
                            ->placeholder('Tulis evaluasi santri di sini...')
                            ->columnSpanFull(),

                        DatePicker::make('recorded_at')
                            ->label('Tanggal Rekam')
                            ->default(now())
                            ->required(),
                    ])->columnSpanFull()
            ]);
    }
}
