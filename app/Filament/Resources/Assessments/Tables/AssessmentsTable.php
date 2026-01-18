<?php

namespace App\Filament\Resources\Assessments\Tables;

use App\Models\AssessmentScore;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column as ExcelColumn;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

// Import yang benar untuk Actions agar tidak error
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;

class AssessmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('classroom.name')->label('Kelas')->searchable()->sortable(),
                TextColumn::make('subject.name')->label('Mata Pelajaran')->searchable()->sortable(),
                TextColumn::make('assessment_type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'daily_test' => 'Ulangan Harian',
                        'quiz' => 'Kuis',
                        'midterm' => 'UTS',
                        'final_exam' => 'UAS',
                        'grade_promotion' => 'UKK',
                        default => $state,
                    }),
                TextColumn::make('assessment_date')->label('Tanggal')->date('d M Y')->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('classroom_id')
                    ->label('Kelas')
                    ->relationship('classroom', 'name'),
                SelectFilter::make('assessment_type')
                    ->label('Jenis Ujian')
                    ->options([
                        'daily_test' => 'Ulangan Harian',
                        'quiz' => 'Kuis',
                        'midterm' => 'UTS',
                        'final_exam' => 'UAS',
                        'grade_promotion' => 'UKK',
                    ]),
                Filter::make('assessment_date')
                    ->label('Rentang Waktu')
                    ->schema([
                        Select::make('preset')
                            ->options(['all' => 'Semua', 'today' => 'Hari ini', 'this_week' => 'Minggu ini', 'this_month' => 'Bulan ini', 'custom' => 'Pilih'])
                            ->default('this_month')->live(),
                        DatePicker::make('from')->visible(fn($get) => $get('preset') === 'custom'),
                        DatePicker::make('until')->visible(fn($get) => $get('preset') === 'custom'),
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
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Unduh Excel Detail')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exports([
                        // Kita panggil mode 'models' di dalam make() agar dia tahu sumber datanya kustom
                        ExcelExport::make('models')
                            ->modifyQueryUsing(function ($query, $livewire) {
                                // Ambil ID Assessment yang terfilter di layar
                                $ids = $livewire->getFilteredTableQuery()->pluck('id');

                                // Paksa query mengambil data AssessmentScore (Detail Siswa)
                                return AssessmentScore::query()
                                    ->whereIn('assessment_id', $ids)
                                    ->with(['student', 'assessment.classroom', 'assessment.subject']);
                            })
                            ->withColumns([
                                ExcelColumn::make('assessment.assessment_type')->heading('Jenis Ujian'),
                                ExcelColumn::make('assessment.classroom.name')->heading('Kelas'),
                                ExcelColumn::make('student.name')->heading('Nama Siswa'),
                                ExcelColumn::make('score')->heading('Nilai'),
                            ])
                            ->askForFilename(date('Y-m-d') . '_laporan_nilai'),
                    ]),
            ])
            // INI SESUAI PESANAN: TIDAK DIGANTI
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
