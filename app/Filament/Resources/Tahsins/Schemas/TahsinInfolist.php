<?php

namespace App\Filament\Resources\Tahsins\Schemas;

use App\Models\Tahsin;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TahsinInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('school_id')
                    ->numeric(),
                TextEntry::make('student_id')
                    ->numeric(),
                TextEntry::make('read_at')
                    ->date(),
                TextEntry::make('type'),
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
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Tahsin $record): bool => $record->trashed()),
            ]);
    }
}
