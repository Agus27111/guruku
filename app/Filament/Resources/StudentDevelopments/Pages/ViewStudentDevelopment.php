<?php

namespace App\Filament\Resources\StudentDevelopments\Pages;

use App\Filament\Resources\StudentDevelopments\StudentDevelopmentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentDevelopment extends ViewRecord
{
    protected static string $resource = StudentDevelopmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
