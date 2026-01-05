<?php

namespace App\Filament\Resources\Tahsins\Pages;

use App\Filament\Resources\Tahsins\TahsinResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTahsins extends ListRecords
{
    protected static string $resource = TahsinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
