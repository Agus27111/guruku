<?php

namespace App\Filament\Resources\NabawiyahActivities\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column as ExcelColumn;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class NabawiyahActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Tanggal (Paling Kiri agar mudah melacak urutan waktu)
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                // 2. Nama Anak (Relasi Many-to-Many)
                TextColumn::make('students.name')
                    ->label('Nama Siswa')
                    ->badge()
                    ->separator(',')
                    ->searchable(),

                // 3. Nama Kegiatan
                TextColumn::make('activity_name')
                    ->label('Kegiatan / Peristiwa')
                    ->searchable()
                    ->description(fn($record) => "Input oleh: {$record->user?->name}"),

                // 4. Pilar yang Terdeteksi (Summary Column)
                // Ini akan mengambil semua kolom 'pilar_' yang bernilai true dan menampilkannya sebagai label
                TextColumn::make('pilar_summary')
                    ->label('Karakter Terdeteksi')
                    ->getStateUsing(function ($record) {
                        $pilars = [];
                        // Ambil semua atribut model yang berawalan 'pilar_' dan bernilai true
                        foreach ($record->getAttributes() as $key => $value) {
                            if (str_starts_with($key, 'pilar_') && $value) {
                                // Ubah 'pilar_syajaaah' menjadi 'Syajaaah'
                                $name = str_replace('pilar_', '', $key);
                                $pilars[] = ucfirst($name);
                            }
                        }
                        return $pilars;
                    })
                    ->badge()
                    ->color('success')
                    ->separator(','),

                TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                // 1. FILTER NAMA SISWA (Hanya siswa di sekolah user tersebut)
                SelectFilter::make('student_id')
                    ->label('Pilih Siswa')
                    ->multiple() // Opsional: agar bisa pilih lebih dari 1 siswa sekaligus
                    ->options(function () {
                        // Ambil school_id dari user yang sedang login
                        $schoolId = auth()->user()->school_id;

                        // Ambil daftar siswa yang hanya berasal dari sekolah tersebut
                        return \App\Models\Student::where('school_id', $schoolId)
                            ->pluck('name', 'id');
                    })
                    ->query(function ($query, array $data) {
                        if (empty($data['values'])) {
                            return $query;
                        }

                        // Karena relasi Many-to-Many (students), kita gunakan whereHas
                        return $query->whereHas('students', function ($q) use ($data) {
                            $q->whereIn('students.id', $data['values']);
                        });
                    }),

                // 2. FILTER TANGGAL DENGAN PRESET (Tetap dipertahankan)
                Filter::make('created_at')
                    ->label('Rentang Waktu')
                    ->schema([
                        Select::make('preset')
                            ->label('Preset Waktu')
                            ->options([
                                'all' => 'Semuanya',
                                'today' => 'Hari ini',
                                'this_week' => 'Minggu ini',
                                'this_month' => 'Bulan ini',
                                'custom' => 'Pilih rentang sendiri',
                            ])
                            ->default('all')
                            ->live(),

                        DatePicker::make('from')
                            ->label('Dari')
                            ->visible(fn($get) => $get('preset') === 'custom'),

                        DatePicker::make('until')
                            ->label('Sampai')
                            ->visible(fn($get) => $get('preset') === 'custom'),
                    ])
                    ->query(function ($query, array $data) {
                        $preset = $data['preset'] ?? 'all';

                        return $query
                            ->when($preset === 'today', fn($q) => $q->whereDate('created_at', Carbon::today()))
                            ->when($preset === 'this_week', fn($q) => $q->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]))
                            ->when($preset === 'this_month', fn($q) => $q->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]))
                            ->when($preset === 'custom', function ($q) use ($data) {
                                return $q
                                    ->when($data['from'], fn($innerQ, $from) => $innerQ->whereDate('created_at', '>=', $from))
                                    ->when($data['until'], fn($innerQ, $until) => $innerQ->whereDate('created_at', '<=', $until));
                            });
                    })
                    ->indicateUsing(function (array $data): array {
                        $preset = $data['preset'] ?? 'all';
                        if ($preset === 'all') return [];

                        return match ($preset) {
                            'today' => ['Hari ini'],
                            'this_week' => ['Minggu ini'],
                            'this_month' => ['Bulan ini'],
                            'custom' => array_filter([
                                ($data['from'] ?? null) ? 'Dari: ' . Carbon::parse($data['from'])->format('d M Y') : null,
                                ($data['until'] ?? null) ? 'Sampai: ' . Carbon::parse($data['until'])->format('d M Y') : null,
                            ]),
                            default => [],
                        };
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Unduh Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename('Jurnal_Nabawiyah_' . date('d-m-Y'))
                            ->withColumns(function () {
                                // 1. Tambahkan kolom utama terlebih dahulu
                                $columns = [
                                    ExcelColumn::make('created_at')
                                        ->heading('Tanggal')
                                        ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d-m-Y')),

                                    ExcelColumn::make('students.name')
                                        ->heading('Nama Siswa'),

                                    ExcelColumn::make('activity_name')
                                        ->heading('Kegiatan'),
                                ];

                                // 2. Daftar 40 Pilar Nabawiyah
                                $pilarKeys = [
                                    'himmah',
                                    'ihsaan',
                                    'izzah',
                                    'waqaar',
                                    'azimah',
                                    'nasyaath',
                                    'firaasah',
                                    'husnuzhan',
                                    'dzakaa',
                                    'hikmah',
                                    'kitmaan',
                                    'satr',
                                    'shidq',
                                    'iffah',
                                    'shamt',
                                    'hayaa',
                                    'qanaah',
                                    'anaah',
                                    'hilm',
                                    'tawaadhu',
                                    'shabr',
                                    'syajaaah',
                                    'ghairah',
                                    'munaafasah',
                                    'nashiihah',
                                    'fashaahah',
                                    'nashrah',
                                    'sakhaa',
                                    'taawun',
                                    'ulfah',
                                    'adaalah',
                                    'wafaa',
                                    'muzaah',
                                    'basyaasyah',
                                    'rifq',
                                    'rahmah',
                                    'mahabbah',
                                    'iitsaar',
                                    'amaanah'
                                ];

                                // 3. Loop untuk membuat kolom Excel secara dinamis
                                foreach ($pilarKeys as $pilar) {
                                    $columns[] = ExcelColumn::make('pilar_' . $pilar)
                                        ->heading(ucfirst($pilar))
                                        ->formatStateUsing(fn($state) => $state ? 1 : 0); // Jika true muncul 1, jika false muncul 0
                                }

                                return $columns;
                            })
                            ->askForFilename(),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ]);
    }
}
