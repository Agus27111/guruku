<?php

namespace App\Filament\Resources\Assessments\Schemas;

use App\Models\Assessment;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AssessmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('school_id')
                    ->numeric(),
                TextEntry::make('student_id')
                    ->numeric(),
                TextEntry::make('teacher_id')
                    ->numeric(),
                TextEntry::make('subject_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('assessment_type'),
                TextEntry::make('assessment_date')
                    ->date(),
                TextEntry::make('score')
                    ->numeric(),
                TextEntry::make('max_score')
                    ->numeric(),
                TextEntry::make('remarks')
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
                    ->visible(fn (Assessment $record): bool => $record->trashed()),
            ]);
    }
}
