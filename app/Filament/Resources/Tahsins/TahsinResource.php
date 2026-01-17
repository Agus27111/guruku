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
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class TahsinResource extends Resource implements HasShieldPermissions
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
        $user = auth()->user();

        // 1. Cek apakah Super Admin (selalu muncul)
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // 2. Cek apakah user menyalakan fitur ini dan apakah dia PRO
        // Kita cek is_pro juga sebagai 'double security'
        return $user->is_pro && $user->is_tahsin_enabled;
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];
    }
}
