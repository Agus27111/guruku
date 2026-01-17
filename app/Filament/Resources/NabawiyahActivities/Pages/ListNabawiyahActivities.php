<?php

namespace App\Filament\Resources\NabawiyahActivities\Pages;

use App\Filament\Resources\NabawiyahActivities\NabawiyahActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNabawiyahActivities extends ListRecords
{
    protected static string $resource = NabawiyahActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

}
