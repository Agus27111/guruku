<?php

namespace App\Filament\Resources\NabawiyahActivities\Pages;

use App\Filament\Resources\NabawiyahActivities\NabawiyahActivityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditNabawiyahActivity extends EditRecord
{
    protected static string $resource = NabawiyahActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
