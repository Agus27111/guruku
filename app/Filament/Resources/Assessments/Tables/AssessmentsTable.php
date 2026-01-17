<?php

namespace App\Filament\Resources\Assessments\Tables;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Illuminate\Support\Carbon;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
// Plugin Excel
use pxlrbt\FilamentExcel\Columns\Column as ExcelColumn;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AssessmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assessment_type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'daily_test' => 'Ulangan Harian',
                        'quiz' => 'Kuis',
                        'midterm' => 'UTS',
                        'final_exam' => 'UAS',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'daily_test' => 'info',
                        'quiz' => 'warning',
                        'midterm' => 'success',
                        'final_exam' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('assessment_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('score')
                    ->label('Nilai')
                    ->description(fn($record) => "Maks: {$record->max_score}")
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->color(function ($state, $record) {
                        if (!$record->max_score || $record->max_score == 0) return 'gray';
                        return ($state / $record->max_score * 100) < 70 ? 'danger' : 'success';
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('assessment_date')
                    ->label('Rentang Waktu')
                    ->schema([
                        Select::make('preset')
                            ->label('Preset Waktu')
                            ->options([
                                'all' => 'Semua Data',
                                'today' => 'Hari ini',
                                'this_week' => 'Minggu ini',
                                'this_month' => 'Bulan ini',
                                'custom' => 'Pilih rentang',
                            ])
                            ->default('this_month')
                            ->live(),

                        DatePicker::make('from')
                            ->label('Dari tanggal')
                            ->visible(fn($get) => $get('preset') === 'custom'),

                        DatePicker::make('until')
                            ->label('Sampai tanggal')
                            ->visible(fn($get) => $get('preset') === 'custom'),
                    ])
                    ->query(function ($query, array $data) {
                        $preset = $data['preset'] ?? 'this_month';
                        if ($preset === 'all') return $query;

                        return $query
                            ->when($preset === 'today', fn($q) => $q->whereDate('assessment_date', Carbon::today()))
                            ->when($preset === 'this_week', fn($q) => $q->whereBetween('assessment_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]))
                            ->when($preset === 'this_month', fn($q) => $q->whereBetween('assessment_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]))
                            ->when(
                                $preset === 'custom',
                                fn($q) => $q
                                    ->when($data['from'], fn($q, $date) => $q->whereDate('assessment_date', '>=', $date))
                                    ->when($data['until'], fn($q, $date) => $q->whereDate('assessment_date', '<=', $date))
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $preset = $data['preset'] ?? 'this_month';
                        return match ($preset) {
                            'today' => ['Tanggal: Hari ini'],
                            'this_week' => ['Tanggal: Minggu ini'],
                            'this_month' => ['Tanggal: Bulan ini'],
                            'custom' => array_filter([
                                ($data['from'] ?? null) ? 'Mulai: ' . Carbon::parse($data['from'])->format('d M Y') : null,
                                ($data['until'] ?? null) ? 'Sampai: ' . Carbon::parse($data['until'])->format('d M Y') : null,
                            ]),
                            default => [],
                        };
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Unduh Excel')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withColumns([
                                ExcelColumn::make('student.name')->heading('Nama Siswa'),
                                ExcelColumn::make('subject.name')->heading('Mata Pelajaran'),
                                ExcelColumn::make('assessment_type')
                                    ->heading('Jenis Penilaian')
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'daily_test' => 'Ulangan Harian',
                                        'quiz' => 'Kuis',
                                        'midterm' => 'UTS',
                                        'final_exam' => 'UAS',
                                        default => $state,
                                    }),
                                ExcelColumn::make('assessment_date')
                                    ->heading('Tanggal')
                                    ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('d-m-Y') : '-'),
                                ExcelColumn::make('score')->heading('Skor'),
                                ExcelColumn::make('max_score')->heading('Skor Maksimal'),
                                ExcelColumn::make('remarks')->heading('Catatan'),
                            ])
                            ->askForFilename(date('Y-m-d') . '_rekap_penilaian'),
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
                    ExportBulkAction::make()->label('Export Terpilih'),
                ]),
            ]);
    }
}
