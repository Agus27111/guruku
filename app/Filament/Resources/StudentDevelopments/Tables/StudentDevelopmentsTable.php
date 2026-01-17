<?php

namespace App\Filament\Resources\StudentDevelopments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Storage;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

class StudentDevelopmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Number')
                    ->label('No')
                    ->state(function ($record, $livewire) {
                        // ambil index record di current page
                        $records = $livewire->getTableRecords();
                        $index = $records->search(fn($r) => $r->getKey() === $record->getKey());

                        return $index === false ? null : $index + 1;
                    }),
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.classroom.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->icon(fn(string $state): string => match ($state) {
                        'Positif' => 'heroicon-o-check-circle',
                        'Negatif' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-minus-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Positif' => 'success', // hijau
                        'Negatif' => 'danger',  // merah
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('date')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y'))
                    ->label('Tanggal')
                    ->sortable(),
                TextColumn::make('photo')
                    ->searchable(),
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
                            return $query->whereDate('date', Carbon::today());
                        }

                        if ($preset === 'this_week') {
                            return $query
                                ->whereDate('date', '>=', Carbon::now()->startOfWeek())
                                ->whereDate('date', '<=', Carbon::now()->endOfWeek());
                        }

                        if ($preset === 'this_month') {
                            return $query
                                ->whereDate('date', '>=', Carbon::now()->startOfMonth())
                                ->whereDate('date', '<=', Carbon::now()->endOfMonth());
                        }

                        // custom range
                        return $query
                            ->when($data['from'] ?? null, fn($q, $from) => $q->whereDate('date', '>=', $from))
                            ->when($data['until'] ?? null, fn($q, $until) => $q->whereDate('date', '<=', $until));
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
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withColumns([
                                Column::make('student.name')->heading('Nama Siswa'),
                                Column::make('student.classroom.name')->heading('Kelas'),
                                Column::make('category')->heading('Kategori'),
                                Column::make('date')
                                    ->heading('Tanggal')
                                    ->formatStateUsing(fn($state) => optional($state)->format('d-m-Y')),
                                // sesuaikan ini dengan nama field kamu:
                                Column::make('note')->heading('Catatan Detail')
                                    ->formatStateUsing(fn($state) => $state ?: null),
                                Column::make('follow_up')->heading('Tindak Lanjut')
                                    ->formatStateUsing(fn($state) => $state ?: null),
                                Column::make('photo')
                                    ->heading('Foto (URL)')
                                    ->formatStateUsing(
                                        fn($state) =>
                                        $state ? Storage::disk('public')->url($state) : null
                                    ),
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
