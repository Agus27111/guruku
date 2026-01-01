<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profil Murid')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('name')
                                ->label('Nama Lengkap')
                                ->weight('bold')
                                ->size('lg'),

                            TextEntry::make('nisn')
                                ->label('NISN')
                                ->copyable()
                                ->placeholder('Belum diisi'),

                            TextEntry::make('classroom.name')
                                ->label('Kelas saat ini')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('created_at')
                                ->label('Terdaftar pada')
                                ->dateTime('d M Y'),
                        ]),
                    ])
            ]);
    }
}
