<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Pages\Settings\JournalSettings;

class WelcomeBanner extends BaseWidget
{
    protected static ?int $sort = -10; // Biar paling atas sendiri

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user = auth()->user();
        $waktu = $this->getWaktuSapaan();

        // Logika WhatsApp
        $waNumber = '62887822368008';
        $waUrl = "https://wa.me/{$waNumber}?text=";

        // Data Dasar (Sapaan)
        $stats = [
            Stat::make("Selamat $waktu,", $user->name)
                ->description($user->school?->name ?? 'Selamat bertugas di JurnalGuruku')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),
        ];

        // Tambahkan Stat "Beli" HANYA jika bukan super_admin DAN belum PRO (atau expired)
        $isExpired = $user->pro_expired_at && now()->gt($user->pro_expired_at);

        if (!$user->hasRole('super_admin') && (!$user->is_pro || $isExpired)) {

            // Link WA untuk Bulanan
            $msgMonthly = urlencode("Halo Admin, saya {$user->name}. Saya ingin aktivasi PRO BULANAN.");
            $stats[] = Stat::make('Paket Bulanan', 'Rp 10.000')
                ->description('Klik untuk aktivasi')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 transition',
                    'onclick' => "window.location.href='" . JournalSettings::getUrl() . "'",
                    'style' => 'cursor: pointer !important;',
                ]);

            // Link WA untuk Tahunan
            $msgYearly = urlencode("Halo Admin, saya {$user->name}. Saya ingin aktivasi PRO TAHUNAN (Hemat).");
            $stats[] = Stat::make('Paket Tahunan', 'Rp 100.000')
                ->description('Hemat Rp 20rb/tahun')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 transition border-2 border-primary-500',
                    'onclick' => "window.location.href='" . JournalSettings::getUrl() . "'",
                    'style' => 'cursor: pointer !important;',
                ]);
        }

        return $stats;
    }

    protected function getWaktuSapaan(): string
    {
        $jam = date('H');
        if ($jam < 11) return 'Pagi';
        if ($jam < 15) return 'Siang';
        if ($jam < 19) return 'Sore';
        return 'Malam';
    }
}
