<?php

namespace App\Filament\Pages\Settings;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Actions\SelectAction;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section as ComponentsSection;
use UnitEnum;

class JournalSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan Jurnal';
    protected static string | UnitEnum | null $navigationGroup = 'Setting Sekolah';
    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.settings.journal-settings';

    public ?array $data = [];

    public function mount(): void
    {
        // Ambil data pivot dari user yang sedang login di sekolah (tenant) aktif
        $pivot = auth()->user()
            ->members()
            ->where('school_id', Filament::getTenant()->id)
            ->first()
            ?->pivot;

        $this->form->fill([
            'is_tahfidz_enabled' => $pivot?->is_tahfidz_enabled ?? true,
            'is_tahsin_enabled' => $pivot?->is_tahsin_enabled ?? true,
            'is_read_enabled' => $pivot?->is_read_enabled ?? true,
        ]);
    }

    public function form($form)
    {
        return $form
            ->schema([
                ComponentsSection::make('Personalisasi Menu Jurnal')
                    ->description('Pilih menu yang ingin Anda tampilkan di sidebar.')
                    ->schema([
                        Toggle::make('is_read_enabled')
                            ->label('Aktifkan Jurnal Baca'),
                        Toggle::make('is_tahfidz_enabled')
                            ->label('Aktifkan Jurnal Tahfidz'),
                        Toggle::make('is_tahsin_enabled')
                            ->label('Aktifkan Jurnal Tahsin'),

                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->color('primary')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $tenantId = Filament::getTenant()->id;

        // Update data ke tabel pivot school_user
        auth()->user()->members()->updateExistingPivot($tenantId, [
            'is_tahfidz_enabled' => $data['is_tahfidz_enabled'],
            'is_tahsin_enabled' => $data['is_tahsin_enabled'],
            'is_read_enabled' => $data['is_read_enabled'],
        ]);

        Notification::make()
            ->success()
            ->title('Pengaturan berhasil disimpan')
            ->body('Silakan refresh halaman jika menu belum berubah.')
            ->send();

        // Opsional: Refresh halaman agar sidebar langsung terupdate
        $this->redirect(static::getUrl());
    }
}
