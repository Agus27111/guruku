<?php

namespace App\Filament\Resources\Tahfidzs;

use App\Filament\Resources\Tahfidzs\Pages\CreateTahfidz;
use App\Filament\Resources\Tahfidzs\Pages\EditTahfidz;
use App\Filament\Resources\Tahfidzs\Pages\ListTahfidzs;
use App\Filament\Resources\Tahfidzs\Pages\ViewTahfidz;
use App\Filament\Resources\Tahfidzs\Schemas\TahfidzForm;
use App\Filament\Resources\Tahfidzs\Schemas\TahfidzInfolist;
use App\Filament\Resources\Tahfidzs\Tables\TahfidzsTable;
use App\Models\Tahfidz;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class TahfidzResource extends Resource
{
    protected static ?string $model = Tahfidz::class;

    protected static ?string $recordTitleAttribute = 'Jurnal Tahfidz';
    protected static ?string $navigationLabel = 'Jurnal Tahfidz';
    protected static string | UnitEnum | null $navigationGroup = 'Jurnal Harian';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-bookmark-square';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return TahfidzForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TahfidzInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TahfidzsTable::configure($table);
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
            'index' => ListTahfidzs::route('/'),
            'create' => CreateTahfidz::route('/create'),
            'view' => ViewTahfidz::route('/{record}'),
            'edit' => EditTahfidz::route('/{record}/edit'),
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

        return $pivot ? (bool) $pivot->is_tahfidz_enabled : true;
    }
}
