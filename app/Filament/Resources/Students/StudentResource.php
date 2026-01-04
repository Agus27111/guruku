<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Schemas\StudentInfolist;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use BackedEnum;
use UnitEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationLabel = 'Daftar Siswa';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string | UnitEnum | null $navigationGroup = 'Setting Sekolah';

    protected static ?int $navigationSort = 100;

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('classroom.name')
                    ->label('Kelas')
                    ->badge()
                    ->sortable(),

                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('classroom_id')
                    ->label('Filter Kelas')
                    ->relationship(
                        'classroom',
                        'name',
                        fn($query) =>
                        $query->where('user_id', auth()->id())
                    ),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
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
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
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
            ->whereHas('classroom', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }
}
