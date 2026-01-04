<?php

namespace App\Filament\Resources\StudentDevelopments\Widgets;

use App\Models\StudentDevelopment as ModelsStudentDevelopment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use StudentDevelopment;

class LatestStudentDevelopments extends TableWidget
{
    protected static ?string $heading = 'Student Development Terbaru';
    protected static ?int $sort = 2;

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

                TextColumn::make('student.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.classroom.name')
                    ->label('Kelas')
                    ->badge()
                    ->sortable(),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'Positif' => 'success',
                        'Negatif' => 'danger',
                        default => 'gray',
                    }),
            ]);
    }

    protected function getQuery(): Builder
    {
        return ModelsStudentDevelopment::query()
            ->where('user_id', auth()->id())
            ->latest('date')
            ->latest('id');
    }
}
