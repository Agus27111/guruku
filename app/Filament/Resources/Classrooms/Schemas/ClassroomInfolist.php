<?php

namespace App\Filament\Resources\Classrooms\Schemas;

use App\Models\Classroom;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClassroomInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
           ->components([
            TextEntry::make('name')
                ->label('Nama Kelas'),
            TextEntry::make('created_at')
                ->label('Dibuat Pada')
                ->dateTime('d M Y H:i'),
            TextEntry::make('deleted_at')
                ->label('Dihapus Pada')
                ->dateTime()
                ->visible(fn (Classroom $record): bool => $record->trashed()),
        ]);
    }
}
