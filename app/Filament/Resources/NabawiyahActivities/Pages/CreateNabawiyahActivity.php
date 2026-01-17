<?php

namespace App\Filament\Resources\NabawiyahActivities\Pages;

use App\Filament\Resources\NabawiyahActivities\NabawiyahActivityResource;
use App\Models\NabawiyahActivity;
use Filament\Resources\Pages\CreateRecord;

class CreateNabawiyahActivity extends CreateRecord
{
    protected static string $resource = NabawiyahActivityResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Isi data otomatis
        $data['user_id'] = auth()->id();
        $data['school_id'] = auth()->user()->school_id;

        // 2. PAKSA HAPUS 'students' dari array data utama 
        // agar tidak masuk ke SQL INSERT nabawiyah_activities
        unset($data['students']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // 3. Simpan relasi secara manual SETELAH record utama dibuat
        // Ini cara paling aman untuk menghindari error "Column not found"
        $data = $this->form->getRawState();

        if (isset($data['students'])) {
            $this->record->students()->sync($data['students']);
        }
    }
}
