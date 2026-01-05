<?php

namespace App\Filament\Resources\Tahsins;

use App\Filament\Resources\Tahsins\Pages\CreateTahsin;
use App\Filament\Resources\Tahsins\Pages\EditTahsin;
use App\Filament\Resources\Tahsins\Pages\ListTahsins;
use App\Filament\Resources\Tahsins\Pages\ViewTahsin;
use App\Filament\Resources\Tahsins\Schemas\TahsinForm;
use App\Filament\Resources\Tahsins\Schemas\TahsinInfolist;
use App\Filament\Resources\Tahsins\Tables\TahsinsTable;
use App\Models\Tahsin;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class TahsinResource extends Resource
{
    protected static ?string $model = Tahsin::class;

    protected static ?string $recordTitleAttribute = 'Jurnal Tahsin';
    protected static ?string $navigationLabel = 'Jurnal Tahsin';
    protected static string | UnitEnum | null $navigationGroup = 'Jurnal Harian';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return TahsinForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TahsinInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TahsinsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTahsins::route('/'),
            'create' => CreateTahsin::route('/create'),
            'view' => ViewTahsin::route('/{record}'),
            'edit' => EditTahsin::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            // Ini memastikan Guru hanya melihat data yang mereka input sendiri
            ->where('user_id', auth()->id())
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if (!$tenant) return false;

        $pivot = auth()->user()->members()
            ->where('school_id', $tenant->id)
            ->first()?->pivot;

        return $pivot ? (bool) $pivot->is_tahsin_enabled : true;
    }
}
