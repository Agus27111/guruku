<?php

namespace App\Filament\Resources\Tahfidzs\Schemas;

use App\Models\Tahfidz;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TahfidzInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('school_id')
                    ->numeric(),
                TextEntry::make('student_id')
                    ->numeric(),
                TextEntry::make('surah'),
                TextEntry::make('juz')
                    ->numeric(),
                TextEntry::make('start_verse')
                    ->numeric(),
                TextEntry::make('end_verse')
                    ->numeric(),
                TextEntry::make('volume')
                    ->placeholder('-'),
                TextEntry::make('page')
                    ->placeholder('-'),
                TextEntry::make('predicate')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('note')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('recorded_at')
                    ->date(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Tahfidz $record): bool => $record->trashed()),
            ]);
    }
}
