<?php

namespace App\Filament\Resources\Nabawiyah\Forms;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Components\Tabs as ComponentsTabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class NabawiyahActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Informasi Kegiatan')
                    ->description('Tuliskan peristiwa atau kegiatan yang terjadi')
                    ->schema([
                        // Pilih Satu Siswa (Wajib)
                        Select::make('students') // Ini merujuk ke relasi belongsToMany kita
                            ->relationship('students', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Nama Siswa')
                            ->helperText('Pilih satu siswa yang ingin dinilai karakternya pada kegiatan ini.'),


                        TextInput::make('activity_name')
                            ->label('Kegiatan / Peristiwa')
                            ->required()
                            ->placeholder('Contoh: Berbagi bekal, Menolong teman jatuh, dll'),

                        Textarea::make('description')
                            ->label('Keterangan / Catatan Guru')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('image')
                            ->label('Foto Dokumentasi')
                            ->image()
                            ->directory('nabawiyah-photos')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),

                ComponentsTabs::make('Karakter Nabawiyah')
                    ->tabs([
                        // TABS 1: INTROVERT (AS-SIRR)
                        Tab::make('INTROVERT (As-Sirr)')
                            ->columns(3)
                            ->schema([
                                // Kelompok Jiwa
                                Toggle::make('pilar_himmah')->label('Himmah (Cita-cita Tinggi)'),
                                Toggle::make('pilar_ihsaan')->label('Ihsaan (Perfeksionis)'),
                                Toggle::make('pilar_izzah')->label('Izzah (Harga Diri)'),
                                Toggle::make('pilar_waqaar')->label('Waqaar (Wibawa)'),
                                Toggle::make('pilar_azimah')->label('Azimah (Tekad)'),
                                Toggle::make('pilar_nasyaath')->label('Nasyaath (Semangat)'),
                                Toggle::make('pilar_firaasah')->label('Firaasah (Firasat/Cerdik)'),
                                Toggle::make('pilar_husnuzhan')->label('Husnuzhan (Prasangka Baik)'),

                                // Kelompok Akal
                                Toggle::make('pilar_dzakaa')->label('Dzakaa (Cerdas)'),
                                Toggle::make('pilar_hikmah')->label('Hikmah'),
                                Toggle::make('pilar_kitmaan')->label('Kitmanus Sirr (Menjaga Rahasia)'),
                                Toggle::make('pilar_satr')->label('Satr (Menutup Aib)'),

                                // Kelompok Perasaan
                                Toggle::make('pilar_shidq')->label('Shidq (Jujur)'),
                                Toggle::make('pilar_iffah')->label('Iffah (Jaga Diri)'),
                                Toggle::make('pilar_shamt')->label('Shamt (Diam)'),
                                Toggle::make('pilar_hayaa')->label('Hayaa (Malu)'),
                                Toggle::make('pilar_qanaah')->label('Qana\'ah (Sederhana)'),
                                Toggle::make('pilar_anaah')->label('Anaah (Tidak Tergesa)'),
                                Toggle::make('pilar_hilm')->label('Hilm (Santun)'),
                                Toggle::make('pilar_tawaadhu')->label('Tawaadhu (Rendah Hati)'),
                                Toggle::make('pilar_shabr')->label('Shabr (Sabar)'),
                            ]),

                        // TABS 2: EXTROVERT (AL-GHALAYAH)
                        Tab::make('EXTROVERT (Al-Ghalayah)')
                            ->columns(3)
                            ->schema([
                                // Kelompok Mempengaruhi
                                Toggle::make('pilar_syajaaah')->label('Syajaa\'ah (Berani)'),
                                Toggle::make('pilar_ghairah')->label('Ghairah (Cemburu)'),
                                Toggle::make('pilar_munaafasah')->label('Munaafasah (Kompetisi)'),
                                Toggle::make('pilar_nashiihah')->label('Nashiihah (Nasehat)'),
                                Toggle::make('pilar_fashaahah')->label('Fashaahah (Fasih Bicara)'),

                                // Kelompok Kerjasama
                                Toggle::make('pilar_nashrah')->label('Nashrah (Menolong)'),
                                Toggle::make('pilar_sakhaa')->label('Sakhaa (Dermawan)'),
                                Toggle::make('pilar_taawun')->label('Ta\'awun (Kerjasama)'),
                                Toggle::make('pilar_ulfah')->label('Ulfah (Bersatu)'),
                                Toggle::make('pilar_adaalah')->label('Adaalah (Adil)'),
                                Toggle::make('pilar_wafaa')->label('Wafaa (Tepat Janji)'),

                                // Kelompok Melayani
                                Toggle::make('pilar_muzaah')->label('Muzaah (Canda)'),
                                Toggle::make('pilar_basyaasyah')->label('Basyaasyah (Berseri-seri)'),
                                Toggle::make('pilar_rifq')->label('Rifq (Lemah Lembut)'),
                                Toggle::make('pilar_rahmah')->label('Rahmah (Belas Kasih)'),
                                Toggle::make('pilar_mahabbah')->label('Mahabbah (Penuh Cinta)'),
                                Toggle::make('pilar_iitsaar')->label('Iitsaar (Melayani)'),
                                Toggle::make('pilar_amaanah')->label('Amaanah (Tanggung Jawab)'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
