<?php

namespace App\Filament\Resources\Assessments\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Assessments\AssessmentResource;

class CreateAssessment extends CreateRecord
{
    protected static string $resource = AssessmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set school_id otomatis
        $data['school_id'] = auth()->user()->school_id;
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record; // Data Assessment yang baru saja dibuat
        $scores = $this->data['assessment_scores'] ?? [];

        foreach ($scores as $item) {
            $record->assessmentScores()->create([
                'student_id' => $item['student_id'],
                'score' => $item['score'],
                'max_score' => 100,
            ]);
        }
    }
}
