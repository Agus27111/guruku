<?php

namespace App\Filament\Pages;

use BackedEnum;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Section;
use Filament\Schemas\Components\Section as ComponentsSection;
use UnitEnum;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Profil Saya';
    protected static ?string $title = 'Edit Profil';
    protected string $view = 'filament.pages.edit-profile';

    protected static string | UnitEnum | null $navigationGroup = 'Setting Sekolah';

    protected static ?int $navigationSort = 300;

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();

        // Ambil data user + nama sekolahnya
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'school_name' => $user->school?->name, // Ambil nama sekolah dari relasi
        ]);
    }

    public function form($form)
    {
        return $form
            ->schema([
                ComponentsSection::make('Informasi Pribadi')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required(),
                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(table: 'users', ignoreRecord: true),
                        ComponentsSection::make('Informasi Sekolah')
                            ->description('Anda dapat memperbarui nama sekolah Anda di sini.')
                            ->schema([
                                TextInput::make('school_name')
                                    ->label('Nama Sekolah')
                                    ->required()
                                    ->placeholder('Masukkan nama sekolah...')
                                    ->helperText('Perubahan ini akan memperbarui nama sekolah di sistem.'),
                            ]),
                    ]),

                ComponentsSection::make('Keamanan')
                    ->description('Kosongkan jika tidak ingin mengganti password')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password Baru')
                            ->password()
                            ->nullable()
                            ->minLength(8)
                            ->dehydrated(fn($state) => filled($state))
                            ->mutateDehydratedStateUsing(fn($state) => Hash::make($state)),
                    ]),
            ])
            ->statePath('data')
            ->model(auth()->user());
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->color('success')
                ->icon('heroicon-m-check-circle')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $user = auth()->user();

        // 1. Update Nama Sekolah jika user punya sekolah
        if ($user->school) {
            $user->school->update([
                'name' => $state['school_name'],
            ]);
        }

        // 2. Update Data User (Nama, Email, Password)
        // Kita hapus school_name dari array agar tidak error saat update user
        $userData = collect($state)->except('school_name')->toArray();
        $user->update($userData);

        Notification::make()
            ->success()
            ->title('Profil dan Sekolah diperbarui!')
            ->send();
    }
}
