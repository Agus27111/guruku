<?php

namespace App\Filament\Resources\Assessments\Schemas;

use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssessmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Penilaian')
                ->description('Pilih mata pelajaran, tipe ujian, dan kelas.')
                ->schema([
                    Grid::make(2)->schema([
                        // 1. Pilih Mata Pelajaran
                        Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->relationship('subject', 'name', fn($query) => $query->where('user_id', auth()->id()))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1)
                            ->createOptionForm([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->label('Nama Mata Pelajaran')
                                        ->placeholder('Contoh: Matematika, Bahasa Inggris...')
                                        ->required()
                                        ->maxLength(100),

                                    TextInput::make('code')
                                        ->label('Kode (Opsional)')
                                        ->placeholder('Contoh: MTK-01')
                                        ->maxLength(20),

                                    Hidden::make('user_id')
                                        ->default(fn() => auth()->id())
                                        ->required(),
                                ]),
                            ]),

                        // 2. Pilih Tipe Assessment
                        Select::make('assessment_type')
                            ->label('Tipe Penilaian')
                            ->options([
                                'quiz' => 'Quiz',
                                'daily_test' => 'Ujian Harian',
                                'midterm' => 'Ujian Tengah Semester (UTS)',
                                'final_exam' => 'Ujian Akhir Semester (UAS)',
                                'grade_promotion' => 'Ujian Kenaikan Kelas (UKK)',
                            ])
                            ->required()
                            ->native(false),

                        // 3. Pilih Kelas (Triger untuk memunculkan siswa)
                        Select::make('classroom_id')
                            ->label('Pilih Kelas')
                            ->relationship('classroom', 'name') // Pastikan ada relasi classroom di model Assessment
                            ->searchable()
                            ->preload()
                            ->live() // Penting agar perubahan langsung dideteksi
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (!$state) {
                                    $set('assessment_scores', []);
                                    return;
                                }

                                // Ambil semua siswa di kelas tersebut
                                $students = Student::where('classroom_id', $state)->get();

                                // Masukkan ke dalam repeater dengan nilai default 0
                                $scores = $students->map(fn($student) => [
                                    'student_id' => $student->id,
                                    'student_name' => $student->name, // Hanya untuk label
                                    'score' => 0,
                                ])->toArray();

                                $set('assessment_scores', $scores);
                            })
                            ->required(),

                        DatePicker::make('assessment_date')
                            ->label('Tanggal Penilaian')
                            ->default(now())
                            ->required(),
                    ]),
                ]),

            Section::make('Input Nilai Siswa')
                ->description('Daftar siswa akan muncul otomatis setelah kelas dipilih.')
                ->schema([
                    // PERBAIKAN 1: Nama harus persis dengan nama fungsi di model: assessmentScores
                    Repeater::make('assessmentScores')
                        ->label('Daftar Nilai')
                        ->relationship()
                        ->schema([
                            Grid::make(3)->schema([
                                // PERBAIKAN 2: Gunakan getStateUsing untuk menarik nama siswa dari relasi
                                TextInput::make('student_name')
                                    ->label('Nama Siswa')
                                    ->formatStateUsing(function ($record, $state) {
                                        // Jika ada record (Edit), ambil nama student. 
                                        // Jika tidak ada (Create), gunakan state yang dikirim dari trigger classroom.
                                        return $record?->student?->name ?? $state;
                                    })
                                    ->disabled()
                                    ->dehydrated(false),

                                Hidden::make('student_id'),

                                TextInput::make('score')
                                    ->label('Nilai')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->required(),
                            ]),
                        ])
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->columns(1),
                ]),

            Section::make('Catatan Tambahan')
                ->schema([
                    Textarea::make('remarks')
                        ->label('Catatan Guru')
                        ->placeholder('Contoh: Rata-rata kelas meningkat...')
                        ->columnSpanFull(),
                ]),

            Hidden::make('user_id')
                ->default(fn() => auth()->id())
                ->required(),
        ]);
    }
}
