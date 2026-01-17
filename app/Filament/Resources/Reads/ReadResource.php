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
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ReadResource extends Resource implements HasShieldPermissions
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
        $user = auth()->user();

        // 1. Cek apakah Super Admin (selalu muncul)
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // 2. Cek apakah user menyalakan fitur ini dan apakah dia PRO
        // Kita cek is_pro juga sebagai 'double security'
        return $user->is_pro && $user->is_read_enabled;
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
