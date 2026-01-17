<?php

namespace App\Filament\Pages\Settings;

use App\Enums\PlanType;
use App\Services\MidtransService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\SelectAction;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section as ComponentsSection;
use Illuminate\Support\Str;
use UnitEnum;

class JournalSettings extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan Jurnal';
    protected static string | UnitEnum | null $navigationGroup = 'Setting Sekolah';
    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.settings.journal-settings';

    public ?array $data = [];

    public function mount(): void
    {
        // Langsung ambil dari user yang sedang login
        $user = auth()->user();

        $this->form->fill([
            'is_nabawiyah_enabled' => $user->is_nabawiyah_enabled ?? true,
            'is_studentDevelopment_enabled' => $user->is_studentDevelopment_enabled ?? true,
            'is_assessment_enabled' => $user->is_assessment_enabled ?? true,
            'is_read_enabled' => $user->is_read_enabled ?? true,
            'is_tahfidz_enabled' => $user->is_tahfidz_enabled ?? true,
            'is_tahsin_enabled' => $user->is_tahsin_enabled ?? true,
        ]);
    }

    public function form($form)
    {
        return $form
            ->schema([
                ComponentsSection::make('Personalisasi Menu Jurnal')
                    ->description('Pilih menu yang ingin Anda tampilkan di sidebar.')
                    ->schema([
                        Toggle::make('is_nabawiyah_enabled')
                            ->label('Aktifkan Karakter Nabawiyah')
                            ->dehydrated(),
                        Toggle::make('is_studentDevelopment_enabled')
                            ->label('Aktifkan Jurnal Pengembangan Siswa')
                            ->disabled(fn() => !auth()->user()->is_pro)
                            // Atau jika ingin lebih aman (cek expired juga)
                            ->disabled(fn() => !auth()->user()->is_pro || (auth()->user()->pro_expired_at && now()->gt(auth()->user()->pro_expired_at)))
                            ->helperText(fn() => !auth()->user()->is_pro ? 'Fitur ini hanya untuk member PRO.' : ''),
                        Toggle::make('is_assessment_enabled')
                            ->label('Aktifkan Jurnal Penilaian')
                            ->disabled(fn() => !auth()->user()->is_pro)
                            // Atau jika ingin lebih aman (cek expired juga)
                            ->disabled(fn() => !auth()->user()->is_pro || (auth()->user()->pro_expired_at && now()->gt(auth()->user()->pro_expired_at)))
                            ->helperText(fn() => !auth()->user()->is_pro ? 'Fitur ini hanya untuk member PRO.' : ''),
                        Toggle::make('is_read_enabled')
                            ->label('Aktifkan Jurnal Baca')
                            ->disabled(fn() => !auth()->user()->is_pro)
                            // Atau jika ingin lebih aman (cek expired juga)
                            ->disabled(fn() => !auth()->user()->is_pro || (auth()->user()->pro_expired_at && now()->gt(auth()->user()->pro_expired_at)))
                            ->helperText(fn() => !auth()->user()->is_pro ? 'Fitur ini hanya untuk member PRO.' : ''),
                        Toggle::make('is_tahfidz_enabled')
                            ->label('Aktifkan Jurnal Tahfidz')
                            ->disabled(fn() => !auth()->user()->is_pro)
                            // Atau jika ingin lebih aman (cek expired juga)
                            ->disabled(fn() => !auth()->user()->is_pro || (auth()->user()->pro_expired_at && now()->gt(auth()->user()->pro_expired_at)))
                            ->helperText(fn() => !auth()->user()->is_pro ? 'Fitur ini hanya untuk member PRO.' : ''),
                        Toggle::make('is_tahsin_enabled')
                            ->label('Aktifkan Jurnal Tahsin')
                            ->disabled(fn() => !auth()->user()->is_pro)
                            // Atau jika ingin lebih aman (cek expired juga)
                            ->disabled(fn() => !auth()->user()->is_pro || (auth()->user()->pro_expired_at && now()->gt(auth()->user()->pro_expired_at)))
                            ->helperText(fn() => !auth()->user()->is_pro ? 'Fitur ini hanya untuk member PRO.' : ''),

                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            // Tombol Simpan Biasa
            Action::make('save')
                ->label('Simpan Perubahan')
                ->color('primary')
                ->action('save'), // Memanggil function save() di class JournalSettings

            // // Tombol Upgrade
            // Action::make('upgrade_pro')
            //     ->label('Upgrade ke PRO')
            //     ->color('success')
            //     ->requiresConfirmation()
            //     ->modalHeading('Konfirmasi Pembayaran')
            //     ->modalDescription('Anda akan melakukan upgrade ke PRO seharga Rp 150.000. Lanjutkan?')
            //     ->modalSubmitActionLabel('Bayar Sekarang')
            //     ->action(function (MidtransService $midtransService) {
            //         try {
            //             $user = auth()->user();

            //             // DI SINI TEMPAT MENGATUR HARGA
            //             $hargaPro = 150000; // Contoh: Rp 150.000

            //             $subscription = \App\Models\Subscription::create([
            //                 'user_id' => $user->id,
            //                 'invoice_number' => 'INV-' . strtoupper(\Illuminate\Support\Str::random(10)),
            //                 'amount' => $hargaPro, // Nilai ini yang dikirim ke Midtrans
            //                 'status' => 'pending',
            //             ]);

            //             // Servis ini akan mengirim 'amount' ke API Midtrans
            //             $snapToken = $midtransService->getSnapToken($subscription);

            //             $this->dispatch('buka-midtrans', token: $snapToken)->toBrowser();
            //         } catch (\Exception $e) {
            //             Notification::make()
            //                 ->danger()
            //                 ->title('Gagal mendapatkan token')
            //                 ->body($e->getMessage())
            //                 ->send();
            //         }
            //     }),
        ];
    }

    // protected function getActions(): array
    // {
    //     return [
    //         Action::make('upgrade_pro')
    //             ->label('Upgrade ke PRO')
    //             ->color('danger')
    //             ->visible(fn() => ! auth()->user()->is_pro)
    //             ->requiresConfirmation()
    //             ->disabled(fn() => auth()->user()->subscription?->status === 'pending')
    //             ->action(function (MidtransService $midtransService) {

    //                 $user = auth()->user();

    //                 $subscription = \App\Models\Subscription::create([
    //                     'user_id' => $user->id,
    //                     'invoice_number' => 'INV-' . Str::upper(Str::random(10)),
    //                     'amount' => 10000,
    //                     'status' => 'pending',
    //                 ]);

    //                 $snapToken = $midtransService->getSnapToken($subscription);

    //                 // ✅ Livewire v3/v4 dispatch
    //                 $this->dispatch('buka-midtrans', token: $snapToken);
    //             }),
    //     ];
    // }

    protected function getActions(): array
    {
        return [
            \Filament\Actions\ActionGroup::make([
                // PAKET BULANAN
                \Filament\Actions\Action::make('upgrade_monthly')
                    ->label('Paket Bulanan (Rp 10.000)')
                    ->icon('heroicon-o-calendar')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->action(fn(MidtransService $service) => $this->prosesUpgrade($service, 'monthly', 10000)),

                // PAKET TAHUNAN
                \Filament\Actions\Action::make('upgrade_yearly')
                    ->label('Paket Tahunan (Rp 100.000)')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(MidtransService $service) => $this->prosesUpgrade($service, 'yearly', 100000)),
            ])
                ->label('Upgrade ke PRO')
                ->icon('heroicon-m-shopping-cart')
                ->color('danger')
                ->button()
                ->visible(fn() => ! auth()->user()->is_pro)
        ];
    }

    // Buat fungsi helper di bawah getActions agar kode tidak duplikat
    protected function prosesUpgrade($midtransService, $planType, $amount)
    {
        $user = auth()->user();

        // Konversi string dari tombol menjadi Enum
        $planEnum = ($planType === 'yearly') ? PlanType::YEARLY : PlanType::MONTHLY;

        $subscription = \App\Models\Subscription::create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(10)),
            'amount' => $amount,
            'plan_type' => $planEnum, // Gunakan objek Enum
            'status' => 'pending',
        ]);

        $snapToken = $midtransService->getSnapToken($subscription);
        $this->dispatch('buka-midtrans', token: $snapToken);
    }


    public function save(): void
    {
        $state = $this->form->getState();
        $user = auth()->user();

        $user->update($state);

        Notification::make()
            ->success()
            ->title('Pengaturan berhasil disimpan')
            ->send();

        $this->redirect(static::getUrl());
    }
}
