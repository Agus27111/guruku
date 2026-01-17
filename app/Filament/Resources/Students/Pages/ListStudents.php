<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use EightyNine\ExcelImport\ExcelImportAction;
use App\Exports\StudentSampleExport;
use App\Models\Classroom;
use Filament\Actions\Action;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExcelImportAction::make()
                ->label('Import Siswa') // Label tombol di halaman list
                ->color('primary')
                ->slideOver()
                // --- Tambahkan ini untuk memperbaiki teks yang error tadi ---
                ->modalHeading('Import Data Siswa dari Excel')
                ->modalDescription('Unggah file excel sesuai template untuk memasukkan data siswa secara massal.')
                ->modalSubmitActionLabel('Mulai Import')
                // -----------------------------------------------------------
                ->mutateBeforeValidationUsing(function (array $data): array {
                    $user = auth()->user();

                    // Langsung ambil dari user, tidak perlu cek tabel pivot
                    if (! $user->school_id) {
                        throw new \Exception('Maaf, akun Anda belum terhubung dengan data Sekolah.');
                    }

                    $data['school_id'] = $user->school_id;
                    return $data;
                })
              
                ->use(\App\Imports\StudentWithClassImport::class)
                ->sampleExcel(
                    sampleData: [], // tidak dipakai karena kita pakai exportClass
                    fileName: 'sample-import-siswa.xlsx',
                    exportClass: StudentSampleExport::class,
                    sampleButtonLabel: 'Download Template Excel',
                    customiseActionUsing: fn(Action $action) => $action
                        ->color('secondary')
                        ->icon('heroicon-o-arrow-down-tray')
                ),
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
