<?php

namespace App\Filament\Resources\StudentDevelopments\Schemas;

use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class StudentDevelopmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Catatan Perkembangan Siswa')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('student_id')
                                ->label('Pilih Siswa')
                                ->relationship(
                                    'student',
                                    'name',
                                    fn($query) =>
                                    $query->whereHas(
                                        'classroom',
                                        fn($q) =>
                                        $q->where('user_id', auth()->id())
                                    )
                                )
                                ->searchable()
                                ->preload()
                                ->live() // PENTING: Agar perubahan langsung dideteksi
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    $studentId = $get('student_id');
                                    if ($studentId) {
                                        // Cari data siswa beserta kelasnya
                                        $student = Student::with('classroom')->find($studentId);
                                        // Set nilai field 'classroom_name' secara otomatis
                                        $set('classroom_display', $student?->classroom?->name ?? '-');
                                    } else {
                                        $set('classroom_display', '');
                                    }
                                })
                                ->required(),

                            TextInput::make('classroom_display')
                                ->label('Kelas')
                                ->disabled()
                                ->dehydrated(false)
                                ->afterStateHydrated(function (Get $get, Set $set, $record) {
                                    if ($record && $record->student) {
                                        $set('classroom_display', $record->student->classroom->name);
                                    }
                                }),

                            DatePicker::make('date')
                                ->label('Tanggal Kejadian')
                                ->default(now())
                                ->required(),
                        ]),

                        Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'Positif' => 'Perilaku Positif / Prestasi',
                                'Negatif' => 'Perilaku Negatif / Pelanggaran',
                            ])
                            ->required()
                            ->native(false),

                        Textarea::make('note')
                            ->label('Catatan Detail')
                            ->placeholder('Jelaskan kejadian atau perkembangan yang terjadi...')
                            ->rows(3)
                            ->required(),

                        Textarea::make('follow_up')
                            ->label('Tindak Lanjut')
                            ->placeholder('Langkah yang diambil oleh guru...')
                            ->rows(2),

                        FileUpload::make('photo')
                            ->label('Bukti Foto (Opsional)')
                            ->image()
                            ->directory('student-developments'),
                    ])
            ]);
    }
}
