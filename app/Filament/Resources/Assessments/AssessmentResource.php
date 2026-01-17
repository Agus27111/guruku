<?php

namespace App\Filament\Resources\Assessments;

use App\Filament\Resources\Assessments\Pages\CreateAssessment;
use App\Filament\Resources\Assessments\Pages\EditAssessment;
use App\Filament\Resources\Assessments\Pages\ListAssessments;
use App\Filament\Resources\Assessments\Pages\ViewAssessment;
use App\Filament\Resources\Assessments\Schemas\AssessmentForm;
use App\Filament\Resources\Assessments\Schemas\AssessmentInfolist;
use App\Filament\Resources\Assessments\Tables\AssessmentsTable;
use App\Models\Assessment;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;


class AssessmentResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Assessment::class;

    protected static ?string $recordTitleAttribute = 'Penilaian';

    protected static ?string $navigationLabel = 'Penilaian';

    protected static string | UnitEnum | null $navigationGroup = 'Jurnal Harian';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?int $navigationSort = 36;


    public static function form(Schema $schema): Schema
    {
        return AssessmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssessmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssessmentsTable::configure($table);
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
            'index' => ListAssessments::route('/'),
            'create' => CreateAssessment::route('/create'),
            'view' => ViewAssessment::route('/{record}'),
            'edit' => EditAssessment::route('/{record}/edit'),
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
        return $user->is_pro && $user->is_assessment_enabled;
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
