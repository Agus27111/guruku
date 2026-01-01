<?php

namespace App\Filament\Resources\StudentDevelopments;

use App\Filament\Resources\StudentDevelopments\Pages\CreateStudentDevelopment;
use App\Filament\Resources\StudentDevelopments\Pages\EditStudentDevelopment;
use App\Filament\Resources\StudentDevelopments\Pages\ListStudentDevelopments;
use App\Filament\Resources\StudentDevelopments\Pages\ViewStudentDevelopment;
use App\Filament\Resources\StudentDevelopments\Schemas\StudentDevelopmentForm;
use App\Filament\Resources\StudentDevelopments\Schemas\StudentDevelopmentInfolist;
use App\Filament\Resources\StudentDevelopments\Tables\StudentDevelopmentsTable;
use App\Models\StudentDevelopment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentDevelopmentResource extends Resource
{
    protected static ?string $model = StudentDevelopment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Student Development';

    public static function form(Schema $schema): Schema
    {
        return StudentDevelopmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentDevelopmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentDevelopmentsTable::configure($table);
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
            'index' => ListStudentDevelopments::route('/'),
            'create' => CreateStudentDevelopment::route('/create'),
            'view' => ViewStudentDevelopment::route('/{record}'),
            'edit' => EditStudentDevelopment::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id()) // Keamanan data antar guru
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
