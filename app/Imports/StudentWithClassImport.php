<?php

namespace App\Imports;

use App\Models\Classroom;
use App\Models\Student;
use EightyNine\ExcelImport\EnhancedDefaultImport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentWithClassImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // Ambil ID sekolah yang sedang aktif dari Filament Tenancy
        $schoolId = \Filament\Facades\Filament::getTenant()->id;
        // Ambil ID user yang sedang login
        $userId = auth()->id();

        DB::transaction(function () use ($rows, $schoolId, $userId) {
            foreach ($rows as $row) {
                $nama  = trim((string) ($row['nama'] ?? ''));
                $nisn  = preg_replace('/\s+/', '', (string) ($row['nisn'] ?? ''));
                $kelas = trim((string) ($row['kelas'] ?? ''));

                if ($nama === '' || $kelas === '') {
                    throw new \Exception("Baris dengan nama '$nama' dan kelas '$kelas' tidak valid.");
                }

                // Tambahkan school_id dan user_id di sini
                $classroom = Classroom::firstOrCreate(
                    [
                        'name' => $kelas,
                        'school_id' => $schoolId, // WAJIB agar tidak error 1364
                    ],
                    [
                        'user_id' => $userId, // Tambahkan juga jika field ini tidak punya default value
                    ]
                );

                if ($nisn !== '') {
                    Student::updateOrCreate(
                        [
                            'nisn' => $nisn,
                            'school_id' => $schoolId, // Pastikan student juga menempel ke sekolah yang sama
                        ],
                        [
                            'name' => $nama,
                            'classroom_id' => $classroom->id,
                        ]
                    );
                } else {
                    Student::create([
                        'name' => $nama,
                        'nisn' => null,
                        'classroom_id' => $classroom->id,
                        'school_id' => $schoolId, // WAJIB
                    ]);
                }
            }
        });
    }
}
