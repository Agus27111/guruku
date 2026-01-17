<?php

namespace App\Imports;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\Failure;

class StudentWithClassImport implements ToCollection, WithHeadingRow, SkipsOnError, SkipsOnFailure
{

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMsg = "Baris " . $failure->row() . " kolom " . $failure->attribute() . ": " . implode(', ', $failure->errors());

            \Filament\Notifications\Notification::make()
                ->title('Gagal Validasi Excel')
                ->body($errorMsg)
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function onError(\Throwable $e)
    {
        \Filament\Notifications\Notification::make()
            ->title('Sistem Error saat Import')
            ->body($e->getMessage())
            ->danger()
            ->send();
    }
    public function collection(Collection $rows)
    {
        try {
            $user = auth()->user();
            $schoolId = $user->school_id;
            $userId = $user->id;

            if (!$schoolId) {
                throw new \Exception("Akun Anda belum terhubung dengan Sekolah.");
            }

            DB::transaction(function () use ($rows, $schoolId, $userId) {
                foreach ($rows as $row) {
                    $nama = trim($row['nama'] ?? '');

                    $nisnRaw = $row['nisn'] ?? null;
                    if (str_contains(strtoupper((string)$nisnRaw), 'E+')) {
                        // Jika format excel scientific, konversi ke string angka murni
                        $nisn = number_format((float)$nisnRaw, 0, '', '');
                    } else {
                        $nisn = preg_replace('/[^0-9]/', '', (string)$nisnRaw);
                    }

                    // 3. Ambil Nama Kelas
                    $kelas = trim($row['kelas'] ?? '');

                    if (empty($nama) || empty($kelas)) {
                        continue;
                    }
                    // 1. Cari atau buat Kelas
                    // Pastikan school_id ikut masuk agar tidak "General Error 1364"
                    $classroom = Classroom::firstOrCreate(
                        [
                            'name' => $kelas,
                            'school_id' => $schoolId,
                            'user_id' => $userId, // Tambahkan ini agar sinkron dengan trait BelongsToUser
                        ]
                    );

                    // 2. Simpan Siswa
                    // Jika NISN diisi, gunakan updateOrCreate. Jika kosong, gunakan create biasa.
                    if (!empty($nisn)) {
                        Student::updateOrCreate(
                            [
                                'nisn' => $nisn,
                                'school_id' => $schoolId,
                            ],
                            [
                                'name' => $nama,
                                'classroom_id' => $classroom->id,
                                'user_id' => $userId,
                            ]
                        );
                    } else {
                        // Cek dulu apakah siswa dengan nama yang sama di kelas yang sama sudah ada
                        // Untuk menghindari duplikasi data tanpa NISN
                        $exists = Student::where('name', $nama)
                            ->where('classroom_id', $classroom->id)
                            ->where('school_id', $schoolId)
                            ->exists();

                        if (!$exists) {
                            Student::create([
                                'name' => $nama,
                                'nisn' => null,
                                'classroom_id' => $classroom->id,
                                'school_id' => $schoolId,
                                'user_id' => $userId,
                            ]);
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            // Log error asli agar kamu bisa baca di storage/logs/laravel.log
            \Illuminate\Support\Facades\Log::error("Import Error: " . $e->getMessage());

            throw $e;
        }
    }
}
