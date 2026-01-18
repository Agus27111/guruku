<?php

namespace App\Filament\Resources\Assessments\Schemas;

use App\Models\Assessment;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class AssessmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Bagian Informasi Utama
                ComponentsSection::make('Informasi Penilaian')
                    ->schema([
                        ComponentsGrid::make(3)
                            ->schema([
                                TextEntry::make('classroom.name')
                                    ->label('Kelas'),

                                TextEntry::make('subject.name')
                                    ->label('Mata Pelajaran')
                                    ->placeholder('-'),

                                TextEntry::make('assessment_type')
                                    ->label('Jenis Penilaian')
                                    ->badge()
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'daily_test' => 'Ulangan Harian',
                                        'quiz' => 'Kuis',
                                        'midterm' => 'UTS',
                                        'final_exam' => 'UAS',
                                        'grade_promotion' => 'UKK',
                                        default => $state,
                                    })
                                    ->color('info'),

                                TextEntry::make('assessment_date')
                                    ->label('Tanggal Pelaksanaan')
                                    ->date('d F Y'),

                                TextEntry::make('user.name')
                                    ->label('Guru Pengampu'),

                                TextEntry::make('remarks')
                                    ->label('Catatan')
                                    ->placeholder('Tidak ada catatan'),
                            ]),
                    ]),

                // Bagian Daftar Nilai Siswa (Dibuat per baris rapi)
                ComponentsSection::make('Daftar Nilai Siswa')
                    ->description('Daftar lengkap siswa beserta nilai yang diperoleh')
                    ->schema([
                        RepeatableEntry::make('assessmentScores') // Relasi ke tabel nilai
                            ->label('')
                            ->schema([
                                ComponentsGrid::make(2)
                                    ->schema([
                                        TextEntry::make('student.name')
                                            ->label('Nama')
                                            ->weight('bold'),

                                        TextEntry::make('score')
                                            ->label('Nilai')
                                            ->badge()
                                            ->color(fn($state) => $state >= 70 ? 'success' : 'danger'),
                                    ]),
                            ])
                            ->columns(1)
                            ->grid(2), // Menampilkan 2 kolom siswa berdampingan agar hemat tempat
                    ]),

                // Bagian Metadata (Waktu input)
                ComponentsSection::make('Audit Trail')
                    ->collapsed() // Disembunyikan secara default agar rapi
                    ->schema([
                        ComponentsGrid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Waktu Input')
                                    ->dateTime(),
                                TextEntry::make('updated_at')
                                    ->label('Terakhir Diperbarui')
                                    ->dateTime(),
                            ]),
                    ]),
            ]);
    }
}
