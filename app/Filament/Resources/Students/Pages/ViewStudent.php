<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;


class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
    protected function getHeaderSubheading(): ?string
    {
        return 'Untuk mengetahui bakat siswa, silakan kunjungi 
        <a href="https://tafsirbakat.com/" target="_blank" class="text-primary underline">
            https://tafsirbakat.com/
        </a>';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\StudentNabawiyahChart::make([
                'record' => $this->record, // Mengirim data siswa aktif ke widget
            ]),
        ];
    }
}
