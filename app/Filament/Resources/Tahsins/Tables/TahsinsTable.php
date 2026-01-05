<?php

namespace App\Filament\Resources\Tahsins\Tables;

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
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column as ExcelColumn;

class TahsinsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Tanggal Baca (Paling kiri agar guru mudah scan waktu)
                TextColumn::make('read_at')
                    ->label('Tanggal')
                    ->date('d M Y') // Format lebih manusiawi: 05 Jan 2026
                    ->sortable(),

                // 2. Nama Santri (Bukan ID angka)
                TextColumn::make('student.name')
                    ->label('Santri')
                    ->searchable()
                    ->sortable(),

                // 3. Metode / Jenis Buku
                TextColumn::make('type')
                    ->label('Metode')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                // 4. Jilid & Halaman (Digabung atau diperjelas)
                TextColumn::make('volume')
                    ->label('Jilid')
                    ->alignCenter(),

                TextColumn::make('page')
                    ->label('Hal.')
                    ->alignCenter(),

                // 5. Predikat dengan Badge & Ikon (Sesuai gaya Tahfidz)
                TextColumn::make('predicate')
                    ->label('Kualitas')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'fluent' => 'Lancar',
                        'Struggling' => 'Kurang Lancar',
                        default => $state,
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'fluent' => 'heroicon-o-check-circle',
                        'Struggling' => 'heroicon-o-exclamation-circle',
                        default => 'heroicon-o-minus-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'fluent' => 'success',    // Hijau
                        'Struggling' => 'warning', // Oranye
                        default => 'gray',
                    })
                    ->sortable(),

                // 6. Timestamps (Disembunyikan secara default)
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('read_at', 'desc') // Menampilkan data terbaru di paling atas
            ->filters([
                TrashedFilter::make(),
                Filter::make('tanggal')
                    ->label('Tanggal')
                    ->schema([
                        Select::make('preset')
                            ->label('Preset')
                            ->options([
                                'today' => 'Hari ini',
                                'this_week' => 'Minggu ini',
                                'this_month' => 'Bulan ini',
                                'custom' => 'Pilih rentang',
                            ])
                            ->default('this_month')
                            ->live(),

                        DatePicker::make('from')
                            ->label('Dari tanggal')
                            ->visible(fn(callable $get) => $get('preset') === 'custom'),

                        DatePicker::make('until')
                            ->label('Sampai tanggal')
                            ->visible(fn(callable $get) => $get('preset') === 'custom'),
                    ])
                    ->query(function ($query, array $data) {
                        $preset = $data['preset'] ?? 'this_month';

                        if ($preset === 'today') {
                            return $query->whereDate('read_at', Carbon::today());
                        }

                        if ($preset === 'this_week') {
                            return $query
                                ->whereDate('read_at', '>=', Carbon::now()->startOfWeek())
                                ->whereDate('read_at', '<=', Carbon::now()->endOfWeek());
                        }

                        if ($preset === 'this_month') {
                            return $query
                                ->whereDate('read_at', '>=', Carbon::now()->startOfMonth())
                                ->whereDate('read_at', '<=', Carbon::now()->endOfMonth());
                        }

                        // custom range
                        return $query
                            ->when($data['from'] ?? null, fn($q, $from) => $q->whereDate('read_at', '>=', $from))
                            ->when($data['until'] ?? null, fn($q, $until) => $q->whereDate('read_at', '<=', $until));
                    })
                    ->indicateUsing(function (array $data): array {
                        $preset = $data['preset'] ?? 'this_month';

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
                            ->withFilename('Laporan_Tahsin_Santri_' . date('d-m-Y'))
                            ->withColumns([
                                ExcelColumn::make('read_at')
                                    ->heading('Tanggal')
                                    ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('d-m-Y') : '-'),

                                ExcelColumn::make('student.name')
                                    ->heading('Nama Santri'),

                                ExcelColumn::make('type')
                                    ->heading('Metode'),

                                ExcelColumn::make('volume')
                                    ->heading('Jilid'),

                                ExcelColumn::make('page')
                                    ->heading('Halaman'),

                                ExcelColumn::make('predicate')
                                    ->heading('Kualitas')
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'fluent' => 'Lancar',
                                        'Struggling' => 'Kurang Lancar',
                                        default => $state,
                                    }),

                                ExcelColumn::make('note') // Jika ada kolom catatan di database
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
