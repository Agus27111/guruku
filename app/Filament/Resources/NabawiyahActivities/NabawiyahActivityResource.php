<?php

namespace App\Filament\Resources\NabawiyahActivities;

use App\Filament\Resources\Nabawiyah\Forms\NabawiyahActivityForm as FormsNabawiyahActivityForm;
use App\Filament\Resources\NabawiyahActivities\Pages\CreateNabawiyahActivity;
use App\Filament\Resources\NabawiyahActivities\Pages\EditNabawiyahActivity;
use App\Filament\Resources\NabawiyahActivities\Pages\ListNabawiyahActivities;
use App\Filament\Resources\NabawiyahActivities\Pages\ViewNabawiyahActivity;
use App\Filament\Resources\NabawiyahActivities\Schemas\NabawiyahActivityForm;
use App\Filament\Resources\NabawiyahActivities\Schemas\NabawiyahActivityInfolist;
use App\Filament\Resources\NabawiyahActivities\Tables\NabawiyahActivitiesTable;
use App\Models\NabawiyahActivity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NabawiyahActivityResource extends Resource
{
    protected static ?string $model = NabawiyahActivity::class;

    protected static ?string $recordTitleAttribute = 'Jurnal Karakter';

    protected static ?string $navigationLabel = 'Jurnal Karakter';

    protected static string | UnitEnum | null $navigationGroup = 'Jurnal Harian';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-lock-open';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return FormsNabawiyahActivityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return NabawiyahActivityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NabawiyahActivitiesTable::configure($table);
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
            'index' => ListNabawiyahActivities::route('/'),
            'create' => CreateNabawiyahActivity::route('/create'),
            'view' => ViewNabawiyahActivity::route('/{record}'),
            'edit' => EditNabawiyahActivity::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        // Menu hanya muncul jika kolom is_nabawiyah_enabled bernilai true
        return auth()->user()->is_nabawiyah_enabled;
    }
}
