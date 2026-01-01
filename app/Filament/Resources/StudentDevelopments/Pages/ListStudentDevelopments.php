<?php

namespace App\Filament\Resources\StudentDevelopments\Pages;

use App\Filament\Resources\StudentDevelopments\StudentDevelopmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentDevelopments extends ListRecords
{
    protected static string $resource = StudentDevelopmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
