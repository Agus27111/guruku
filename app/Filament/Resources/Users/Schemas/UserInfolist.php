<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('is_pro')
                    ->boolean(),
                TextEntry::make('pro_expired_at')
                    ->date()
                    ->placeholder('-'),
                IconEntry::make('is_tahfidz_enabled')
                    ->boolean(),
                IconEntry::make('is_tahsin_enabled')
                    ->boolean(),
                IconEntry::make('is_read_enabled')
                    ->boolean(),
                IconEntry::make('is_studentDevelopment_enabled')
                    ->boolean(),
                IconEntry::make('is_assessment_enabled')
                    ->boolean(),
                TextEntry::make('school_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
