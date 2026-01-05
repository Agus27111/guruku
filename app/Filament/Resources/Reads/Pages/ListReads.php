<?php

namespace App\Filament\Resources\Reads\Pages;

use App\Filament\Resources\Reads\ReadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReads extends ListRecords
{
    protected static string $resource = ReadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
