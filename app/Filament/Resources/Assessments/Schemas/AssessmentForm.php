<?php

namespace App\Filament\Resources\Assessments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssessmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Assessment Information')
                ->description('Select student, subject, and assessment type.')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('student_id')
                            ->label('Student')
                            ->relationship('student', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('subject_id')
                            ->label('Subject')
                            ->relationship(
                                'subject',
                                'name',
                                fn($query) => $query->where('user_id', auth()->id())
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Subject Name')
                                    ->required()
                                    ->maxLength(100),

                                TextInput::make('code')
                                    ->label('Subject Code')
                                    ->maxLength(20),

                                Hidden::make('user_id')
                                    ->default(fn() => auth()->id())
                                    ->required(),
                            ]),

                        Select::make('assessment_type')
                            ->label('Assessment Type')
                            ->options([
                                'daily_test' => 'Daily Test',
                                'quiz' => 'Quiz',
                                'midterm' => 'Midterm Exam',
                                'final_exam' => 'Final Exam',
                            ])
                            ->required()
                            ->native(false),

                        DatePicker::make('assessment_date')
                            ->label('Assessment Date')
                            ->default(now())
                            ->required(),
                    ]),
                ]),

            Section::make('Score Result')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('score')
                            ->label('Score')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01),

                        TextInput::make('max_score')
                            ->label('Maximum Score')
                            ->numeric()
                            ->default(100)
                            ->required()
                            ->step(0.01),
                    ]),

                    Textarea::make('remarks')
                        ->label('Teacher Remarks')
                        ->placeholder('Write a brief evaluation...')
                        ->columnSpanFull(),
                ]),

            Hidden::make('user_id')
                ->default(fn() => auth()->id())
                ->required(),
        ]);
    }
}
