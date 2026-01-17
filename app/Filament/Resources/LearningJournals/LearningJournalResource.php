<?php

namespace App\Filament\Resources\LearningJournals;

use App\Filament\Resources\LearningJournals\Pages\CreateLearningJournal;
use App\Filament\Resources\LearningJournals\Pages\EditLearningJournal;
use App\Filament\Resources\LearningJournals\Pages\ListLearningJournals;
use App\Filament\Resources\LearningJournals\Pages\ViewLearningJournal;
use App\Filament\Resources\LearningJournals\Schemas\LearningJournalForm;
use App\Filament\Resources\LearningJournals\Schemas\LearningJournalInfolist;
use App\Filament\Resources\LearningJournals\Tables\LearningJournalsTable;
use App\Models\LearningJournal;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class LearningJournalResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = LearningJournal::class;
    protected static ?string $recordTitleAttribute = 'Learning Journal';
    protected static ?string $navigationLabel = 'Daftar Jurnal Belajar';
    protected static string | UnitEnum | null $navigationGroup = 'Jurnal Harian';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    //urtutan menu
    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return LearningJournalForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LearningJournalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LearningJournalsTable::configure($table);
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
            'index' => ListLearningJournals::route('/'),
            'create' => CreateLearningJournal::route('/create'),
            'view' => ViewLearningJournal::route('/{record}'),
            'edit' => EditLearningJournal::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    //filter data sesuai user login
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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
