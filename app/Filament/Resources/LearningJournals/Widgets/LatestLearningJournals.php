<?php

namespace App\Filament\Resources\LearningJournals\Widgets;

use App\Models\LearningJournal;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use LearningJournals;

class LatestLearningJournals extends TableWidget
{
    protected static ?string $heading = 'Jurnal Belajar Terakhir';
    protected static ?int $sort = 1;

    // jumlah kolom grid (1 = full width, 2 = setengah, dst)
    protected int|string|array $columnSpan = 'full';
   public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->defaultPaginationPageOption(5)
            ->columns([
               TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

               TextColumn::make('classroom.name')
                    ->label('Kelas')
                    ->badge()
                    ->sortable(),

               TextColumn::make('subject.name')
                    ->label('Mapel')
                    ->sortable(),

               TextColumn::make('topic')
                    ->label('Topik')
                    ->limit(30)
                    ->searchable(),
            ]);

    }

    protected function getQuery(): Builder
    {
        return LearningJournal::query()
            ->where('user_id', auth()->id())
            ->latest('date')      // urutkan by tanggal pelaksanaan
            ->latest('id');       // fallback kalau tanggal sama
    }
}
