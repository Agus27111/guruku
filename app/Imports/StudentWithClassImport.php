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
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Ambil dari header Excel: nama, nisn, kelas
                $nama  = trim((string) ($row['nama'] ?? ''));
                $nisn  = preg_replace('/\s+/', '', (string) ($row['nisn'] ?? ''));
                $kelas = trim((string) ($row['kelas'] ?? ''));

                // nama & kelas wajib
                if ($nama === '' || $kelas === '') {
                    throw new \Exception("Baris dengan nama '$nama' dan kelas '$kelas' tidak valid. Nama dan kelas wajib diisi.");
                }

                // Buat / cari kelas
                $classroom = Classroom::firstOrCreate([
                    'name' => $kelas,
                ]);

                // NISN ada -> update/insert by nisn
                if ($nisn !== '') {
                    Student::updateOrCreate(
                        ['nisn' => $nisn],
                        [
                            'name' => $nama,
                            'classroom_id' => $classroom->id,
                        ]
                    );
                } else {
                    // NISN kosong -> selalu create baru
                    Student::create([
                        'name' => $nama,
                        'nisn' => null,
                        'classroom_id' => $classroom->id,
                    ]);
                }
            }
        });
    }
}
