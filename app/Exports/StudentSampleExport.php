<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentSampleExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'nama',
            'nisn',
            'kelas',
        ];
    }

    public function array(): array
    {
        return [
            [
                'nama'  => 'Ahmad Fulan',
                'nisn'  => '1234567890',
                'kelas' => '1A',
            ],
            [
                'nama'  => 'Aisyah',
                'nisn'  => null,
                'kelas' => '1A',
            ],
        ];
    }
}
