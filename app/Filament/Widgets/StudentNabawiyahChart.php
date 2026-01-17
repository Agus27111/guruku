<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\ChartWidget;

class StudentNabawiyahChart extends ChartWidget
{
    protected ?string $heading = 'Grafik 40 Pilar Karakter Nabawiyah';
    protected int | string | array $columnSpan = 'full';
    public ?Student $record = null;

    protected function getData(): array
    {
        if (!$this->record) {
            return ['datasets' => [], 'labels' => []];
        }

        // Ambil data dari model
        $stats = $this->record->getNabawiyahStats();
        // dd($stats);
        $groups = [
            'KERJA KERAS' => ['himmah', 'ihsaan', 'izzah', 'waqaar', 'azimah', 'nasyaath'],
            'KECERDASAN'  => ['firaasah', 'husnuzhan', 'dzakaa', 'hikmah', 'kitmaan', 'satr'],
            'KEJUJURAN'   => ['shidq', 'iffah', 'shamt', 'hayaa', 'qanaah'],
            'RENDAH HATI' => ['anaah', 'hilm', 'tawaadhu', 'shabr'],
            'KEBERANIAN'  => ['syajaaah', 'ghairah', 'munaafasah'],
            'KEPEDULIAN'  => ['nashiihah', 'fashaahah', 'nashrah', 'sakhaa', 'taawun', 'ulfah'],
            'KEADILAN'    => ['adaalah', 'wafaa', 'muzaah', 'basyaasyah'],
            'KASIH SAYANG' => ['rifq', 'rahmah', 'mahabbah', 'iitsaar', 'amaanah'],
        ];

        $labels = [];
        $dataValues = [];
        $backgroundColors = [];

        $groupColors = [
            'KERJA KERAS' => '#4f46e5',
            'KECERDASAN'  => '#0ea5e9',
            'KEJUJURAN'   => '#10b981',
            'RENDAH HATI' => '#f59e0b',
            'KEBERANIAN'  => '#ef4444',
            'KEPEDULIAN'  => '#8b5cf6',
            'KEADILAN'    => '#ec4899',
            'KASIH SAYANG' => '#64748b',
        ];

        foreach ($groups as $groupName => $pilars) {
            foreach ($pilars as $pilar) {
                $labels[] = ucfirst($pilar);

                // CEK KEY: Coba pakai awalan pilar_ atau langsung namanya
                $val = 0;
                if (isset($stats['pilar_' . $pilar])) {
                    $val = $stats['pilar_' . $pilar];
                } elseif (isset($stats[$pilar])) {
                    $val = $stats[$pilar];
                }

                $dataValues[] = (int) $val; // Paksa jadi integer
                $backgroundColors[] = $groupColors[$groupName];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Poin',
                    'data' => $dataValues,
                    'backgroundColor' => $backgroundColors,
                    'borderRadius' => 4, // Agar bar lebih cantik
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // Mengubah chart menjadi horizontal agar 40 pilar terbaca jelas
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1],
                ],
                'y' => [
                    'ticks' => [
                        'autoSkip' => false, // Paksa semua 40 nama pilar muncul
                        'font' => ['size' => 10],
                    ],
                ],
            ],
            'plugins' => [
                'legend' => ['display' => false], // Sembunyikan legend karena warna sudah per kelompok
            ],
        ];
    }

    public static function canView(): bool
    {
        return request()->routeIs('filament.admin.resources.students.view');
    }
}
