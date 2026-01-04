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
                    $schoolId = \Filament\Facades\Filament::getTenant()->id;

                    $data['name'] = $data['nama'] ?? $data['name'] ?? null;
                    $kelas = trim((string) ($data['kelas'] ?? ''));

                    if ($kelas !== '') {
                        $classroom = Classroom::firstOrCreate([
                            'name' => $kelas,
                            'school_id' => $schoolId,
                        ], [
                            'user_id' => auth()->id(),
                        ]);

                        $data['classroom_id'] = $classroom->id;
                    }

                    $data['school_id'] = $schoolId;
                    unset($data['nama'], $data['kelas']);

                    return $data;
                })
                // (opsional) validasi setelah mapping
                ->validateUsing([
                    'name' => 'required',
                    'classroom_id' => 'required|integer',
                    'nisn' => 'nullable',
                ])
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
