<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Tables\Columns\ToggleColumn;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn($record) => $record->email),

                TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->badge()
                    ->color('info')
                    ->placeholder('Tanpa Sekolah')
                    ->searchable(),

                // MENGGUNAKAN TOGGLE AGAR BISA EDIT MANUAL LANGSUNG DI TABEL
                ToggleColumn::make('is_pro')
                    ->label('PRO')
                    ->sortable(),

                TextColumn::make('pro_expired_at')
                    ->label('Expired')
                    ->date()
                    ->sortable()
                    ->color(fn($record) => $record->pro_expired_at?->isPast() ? 'danger' : 'gray'),


                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 1. Filter Status PRO (Ya, Tidak, atau Semua)
                TernaryFilter::make('is_pro')
                    ->label('Status PRO')
                    ->placeholder('Semua User')
                    ->trueLabel('Hanya User PRO')
                    ->falseLabel('Hanya User Free'),

                // 2. Filter Masa Berlaku PRO
                SelectFilter::make('pro_status')
                    ->label('Masa Berlaku PRO')
                    ->options([
                        'active' => 'Sedang Aktif',
                        'expiring_soon' => 'Akan Habis (7 Hari)',
                        'expired' => 'Sudah Kadaluarsa',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'] === 'active',
                                fn(Builder $query) => $query->where('is_pro', true)->where('pro_expired_at', '>', now())
                            )
                            ->when(
                                $data['value'] === 'expiring_soon',
                                fn(Builder $query) => $query->where('is_pro', true)
                                    ->where('pro_expired_at', '>', now())
                                    ->where('pro_expired_at', '<=', now()->addDays(7))
                            )
                            ->when(
                                $data['value'] === 'expired',
                                fn(Builder $query) => $query->where('pro_expired_at', '<=', now())
                            );
                    }),

                // 3. Filter berdasarkan Sekolah
                SelectFilter::make('school_id')
                    ->label('Filter Sekolah')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload(),
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
