<?php

namespace App\Filament\Resources\Reads;

use App\Filament\Resources\Reads\Pages\CreateRead;
use App\Filament\Resources\Reads\Pages\EditRead;
use App\Filament\Resources\Reads\Pages\ListReads;
use App\Filament\Resources\Reads\Pages\ViewRead;
use App\Filament\Resources\Reads\Schemas\ReadForm;
use App\Filament\Resources\Reads\Schemas\ReadInfolist;
use App\Filament\Resources\Reads\Tables\ReadsTable;
use App\Models\Read;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ReadResource extends Resource
{
    protected static ?string $model = Read::class;

    protected static ?string $recordTitleAttribute = 'Jurnal Baca';

    protected static ?string $navigationLabel = 'Jurnal Baca';

    protected static string | UnitEnum | null $navigationGroup = 'Jurnal Harian';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return ReadForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReadsTable::configure($table);
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
            'index' => ListReads::route('/'),
            'create' => CreateRead::route('/create'),
            'view' => ViewRead::route('/{record}'),
            'edit' => EditRead::route('/{record}/edit'),
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

        return $pivot ? (bool) $pivot->is_read_enabled : true;
    }
}
