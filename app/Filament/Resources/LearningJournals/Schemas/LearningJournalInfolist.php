<?php

namespace App\Filament\Resources\LearningJournals\Schemas;

use App\Models\LearningJournal;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\ImageEntry; // Entry data tetap di Infolists
use Filament\Infolists\Components\TextEntry;  // Entry data tetap di Infolists
use Filament\Schemas\Components\Section;      // Section pindah ke Schemas
use Filament\Schemas\Components\Grid;         // Jika pakai Grid, ambil dari Schemas

class LearningJournalInfolist
{
    /**
     * Kita gunakan Filament\Schemas\Schema sesuai sistem Filament v4
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Jurnal Pembelajaran')
                    ->columns(2)
                    ->schema([
                        TextEntry::make("classroom.name")
                            ->label('Nama Kelas')
                            ->weight('bold')
                            ->color('primary'),

                        TextEntry::make('date')
                            ->label('Tanggal Pelaksanaan')
                            ->date('d F Y'),

                        TextEntry::make('teaching_hours')
                            ->label('Jam Ke-')
                            ->badge()
                            ->color('success'),

                        TextEntry::make('topic')
                            ->label('Topik Materi'),

                        TextEntry::make('activity')
                            ->label('Kegiatan')
                            ->columnSpanFull(),

                        ImageEntry::make('photo')
                            ->label('Foto Dokumentasi')
                            ->disk('public')
                            ->columnSpanFull(),
                    ])
            ]);
    }
}