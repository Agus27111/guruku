<?php

namespace App\Filament\Resources\LearningJournals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
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

class LearningJournalsTable
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
                TextColumn::make('classroom.name')
                    ->label('Kelas')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Tanggal Pelaksanaan')
                    //bikin format dengan ada nama harinya
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y'))
                    ->sortable(),
                TextColumn::make('topic')
                    ->searchable(),
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
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withColumns([
                                Column::make('classroom.name')->heading('Kelas'),
                                Column::make('subject.name')->heading('Mata Pelajaran'),
                                Column::make('date')
                                    ->heading('Tanggal')
                                    ->formatStateUsing(fn($state) => optional($state)->format('d-m-Y')),

                                Column::make('topic')->heading('Topik'),
                                Column::make('activity')->heading('Aktivitas'),
                                Column::make('note')->heading('Catatan'),

                                // ✅ FOTO JADI URL BISA DIKLIK
                                Column::make('photo')
                                    ->heading('Foto')
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
