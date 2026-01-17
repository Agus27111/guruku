<?php

namespace App\Filament\Resources\Reads\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use pxlrbt\FilamentExcel\Columns\Column as ExcelColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Container\Attributes\Storage;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ReadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Tanggal Baca (Format yang lebih enak dibaca)
                TextColumn::make('read_at')
                    ->label('Tanggal')
                    ->date('l, d M Y')
                    ->sortable(),

                // 2. Nama Santri (Relationship)
                TextColumn::make('student.name')
                    ->label('Santri')
                    ->searchable()
                    ->sortable(),

                // 3. Nama Metode Baca (Disimpan di kolom 'type')
                TextColumn::make('type')
                    ->label('Metode Baca')
                    ->searchable()
                    ->description(fn($record) => "Jilid: {$record->volume}"),

                // 4. Progres Halaman
                TextColumn::make('page')
                    ->label('Hal.')
                    ->formatStateUsing(fn($state) => "Hal. {$state}")
                    ->alignCenter(),

                // 5. Predikat dengan Visual Badge & Ikon
                TextColumn::make('predicate')
                    ->label('Kualitas')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'fluent' => 'Lancar',
                        'Struggling' => 'Kurang Lancar',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'fluent' => 'success',
                        'Struggling' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'fluent' => 'heroicon-o-check-circle',
                        'Struggling' => 'heroicon-o-exclamation-circle',
                        default => 'heroicon-o-minus-circle',
                    }),

                // 6. Timestamps (Hidden by default)
                TextColumn::make('created_at')
                    ->label('Input Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('read_at', 'desc') // Data terbaru muncul di atas
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
                            ->withFilename('Laporan_Baca_Santri_' . date('d-m-Y'))
                            ->withColumns([
                                // Gunakan ExcelColumn, bukan Column biasa
                                ExcelColumn::make('read_at')
                                    ->heading('Tanggal')
                                    ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('d-m-Y') : '-'),

                                ExcelColumn::make('student.name')
                                    ->heading('Nama Santri'),

                                ExcelColumn::make('type')
                                    ->heading('Metode Baca'),

                                ExcelColumn::make('volume')
                                    ->heading('Jilid'),

                                ExcelColumn::make('page')
                                    ->heading('Halaman'),

                                ExcelColumn::make('predicate')
                                    ->heading('Kualitas/Predikat')
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'fluent' => 'Lancar',
                                        'Struggling' => 'Kurang Lancar',
                                        default => $state,
                                    }),
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
