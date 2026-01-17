<?php

namespace App\Filament\Resources\NabawiyahActivities\Pages;

use App\Filament\Resources\NabawiyahActivities\NabawiyahActivityResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNabawiyahActivity extends ViewRecord
{
    protected static string $resource = NabawiyahActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
