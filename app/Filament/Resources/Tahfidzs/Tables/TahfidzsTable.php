<?php

namespace App\Filament\Resources\Tahfidzs\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column as ExcelColumn;

class TahfidzsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recorded_at')
                    ->date()
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y'))
                    ->label('Tanggal')
                    ->sortable(),
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('surah')
                    ->description(fn($record) => "Juz: {$record->juz}")
                    ->searchable(),
                TextColumn::make('start_verse')
                    ->label('Rentang Ayat')
                    ->formatStateUsing(function ($record): string {
                        // Menggabungkan start_verse dan end_verse
                        return "Ayat {$record->start_verse} - {$record->end_verse}";
                    })
                    ->searchable(['start_verse', 'end_verse']) // Tetap bisa dicari berdasarkan angka ayat
                    ->sortable(['start_verse']) // Diurutkan berdasarkan ayat mulai
                    ->alignCenter(),
                TextColumn::make('predicate')
                    ->label('Kualitas Hafalan')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'fluent' => 'Lancar',
                        'Struggling' => 'Kurang Lancar',
                        default => $state,
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'fluent' => 'heroicon-o-check-badge',
                        'Struggling' => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-minus-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'fluent' => 'success',    // Hijau
                        'Struggling' => 'warning', // Oranye/Kuning
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('note')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('tanggal')
                    ->label('Tanggal')
                    ->schema([
                        Select::make('preset')
                            ->label('Preset')
                            ->options([
                                'all' => 'Semuanya',
                                'today' => 'Hari ini',
                                'this_week' => 'Minggu ini',
                                'this_month' => 'Bulan ini',
                                'custom' => 'Pilih rentang',
                            ])
                            ->default('all')
                            ->live(),

                        DatePicker::make('from')
                            ->label('Dari tanggal')
                            ->visible(fn(callable $get) => $get('preset') === 'custom'),

                        DatePicker::make('until')
                            ->label('Sampai tanggal')
                            ->visible(fn(callable $get) => $get('preset') === 'custom'),
                    ])
                    ->query(function ($query, array $data) {
                        $preset = $data['preset'] ?? 'all';

                        if ($preset === 'all') {
                            return $query;
                        }

                        if ($preset === 'today') {
                            return $query->whereDate('recorded_at', Carbon::today());
                        }

                        if ($preset === 'this_week') {
                            return $query
                                ->whereDate('recorded_at', '>=', Carbon::now()->startOfWeek())
                                ->whereDate('recorded_at', '<=', Carbon::now()->endOfWeek());
                        }

                        if ($preset === 'this_month') {
                            return $query
                                ->whereDate('recorded_at', '>=', Carbon::now()->startOfMonth())
                                ->whereDate('recorded_at', '<=', Carbon::now()->endOfMonth());
                        }

                        // custom range
                        return $query
                            ->when($data['from'] ?? null, fn($q, $from) => $q->whereDate('recorded_at', '>=', $from))
                            ->when($data['until'] ?? null, fn($q, $until) => $q->whereDate('recorded_at', '<=', $until));
                    })
                    ->indicateUsing(function (array $data): array {
                        $preset = $data['preset'] ?? 'all';

                        if ($preset === 'all') {
                            return [];
                        }

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
                    })

            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Unduh Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename('Laporan_Tahfidz_Santri_' . date('d-m-Y'))
                            ->withColumns([
                                ExcelColumn::make('recorded_at')
                                    ->heading('Tanggal')
                                    ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('d-m-Y') : '-'),

                                ExcelColumn::make('student.name')
                                    ->heading('Nama Siswa'),

                                ExcelColumn::make('surah')
                                    ->heading('Surah'),

                                ExcelColumn::make('juz')
                                    ->heading('Juz'),

                                ExcelColumn::make('start_verse')
                                    ->heading('Ayat Mulai'),

                                ExcelColumn::make('end_verse')
                                    ->heading('Ayat Selesai'),

                                ExcelColumn::make('predicate')
                                    ->heading('Kualitas Hafalan')
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'fluent' => 'Lancar',
                                        'Struggling' => 'Kurang Lancar',
                                        default => $state,
                                    }),

                                ExcelColumn::make('note')
                                    ->heading('Catatan'),
                            ])
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
