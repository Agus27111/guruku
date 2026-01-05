<?php

namespace App\Filament\Resources\Tahsins\Pages;

use App\Filament\Resources\Tahsins\TahsinResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTahsin extends ViewRecord
{
    protected static string $resource = TahsinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
