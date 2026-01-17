<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        // Jangan tampilkan widget untuk Super Admin
        if ($user->hasRole('super_admin')) {
            return [];
        }

        $status = $user->is_pro ? 'PRO' : 'FREE';
        $color = $user->is_pro ? 'success' : 'gray';

        $diffDays = 0;
        if ($user->pro_expired_at) {
            $diffDays = now()->diffInDays($user->pro_expired_at, false);
            $diffDays = ceil($diffDays); // Pembulatan ke atas
            $diffDays = max(0, (int)$diffDays); // Hindari angka negatif
        }

        $description = $user->is_pro
            ? "Berakhir dalam {$diffDays} hari"
            : "Upgrade ke PRO untuk fitur lengkap";

        return [
            Stat::make('Status Akun', $status)
                ->description($description)
                ->descriptionIcon($user->is_pro ? 'heroicon-m-check-badge' : 'heroicon-m-arrow-trending-up')
                ->color($color),

            Stat::make('Sekolah', $user->school?->name ?? 'Belum Terdaftar')
                ->description('Data terisolasi untuk sekolah ini')
                ->color('info'),

            // Stat 2: Jumlah Siswa
            Stat::make('Jumlah Siswa', $user->students()->count() . ' Orang')
                ->description('Total siswa terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->chart([3, 5, 4, 7, 8, 10, auth()->user()->students()->count()])
                ->color('primary'),
        ];
    }
}
