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
                ->color('primary')
                ->slideOver()
                // ✅ Ubah "nama/kelas" jadi "name/classroom_id"
                ->mutateBeforeValidationUsing(function (array $data): array {
                    // map nama -> name
                    $data['name'] = $data['nama'] ?? $data['name'] ?? null;

                    // map kelas -> classroom_id
                    $kelas = trim((string) ($data['kelas'] ?? ''));
                    if ($kelas !== '') {
                        $classroom = Classroom::firstOrCreate(['name' => $kelas]);
                        $data['classroom_id'] = $classroom->id;
                    }

                    // buang kolom yang tidak ada di tabel students
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
