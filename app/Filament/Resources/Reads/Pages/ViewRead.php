<?php

namespace App\Filament\Resources\Reads\Pages;

use App\Filament\Resources\Reads\ReadResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRead extends ViewRecord
{
    protected static string $resource = ReadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
