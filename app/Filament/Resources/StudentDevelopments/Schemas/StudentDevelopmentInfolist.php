<?php

namespace App\Filament\Resources\StudentDevelopments\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class StudentDevelopmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Perkembangan')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('student.name')
                                ->label('Nama Siswa')
                                ->weight('bold'),

                            TextEntry::make('date')
                                ->label('Tanggal')
                                ->date('d M Y'),

                            TextEntry::make('category')
                                ->label('Kategori')
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    'Positif' => 'success',
                                    'Negatif' => 'danger',
                                    default => 'gray',
                                }),
                        ]),

                        TextEntry::make('note')
                            ->label('Catatan Perkembangan')
                            ->prose(),

                        TextEntry::make('follow_up')
                            ->label('Tindak Lanjut')
                            ->placeholder('Belum ada tindak lanjut'),

                        ImageEntry::make('photo')
                            ->label('Dokumentasi')
                            ->disk('public')
                            ->visibility('public'),
                    ])
            ]);
    }
}
