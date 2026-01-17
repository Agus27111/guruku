<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Spatie\Permission\Models\Role; // Tambahkan import ini

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. --- SETUP ROLE ---
        // Membuat role super_admin jika belum ada
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $panelUserRole = Role::firstOrCreate(['name' => 'panel_user']); // Role umum untuk guru

        // Tanggal untuk Lifetime (100 tahun ke depan)
        $lifetime = Carbon::now()->addYears(100);

        // 1. --- SUPER ADMIN ---
        $admin = User::factory()->create([
            'name' => 'Super Admin Guruku',
            'email' => 'admin@guruku.com',
            'password' => Hash::make('49u5'),
            'school_id' => null,
            'is_pro' => true,
            'pro_expired_at' => $lifetime,
        ]);

        // Berikan role super_admin ke akun ini
        $admin->assignRole($superAdminRole);

        // 2. --- SEKOLAH DEMO ---
        $schoolDemo = School::create([
            'name' => 'SMPN 4 Terisi',
        ]);

        // Akun Khusus: Hanifah Fitriani (PRO Lifetime)
        $user1 = User::factory()->create([
            'name' => 'Hanifah Fitriani',
            'email' => 'hanifahfitriani11@guru.smp.belajar.id',
            'password' => Hash::make('12345'),
            'school_id' => $schoolDemo->id,
            'is_pro' => true,
            'pro_expired_at' => $lifetime,
            'is_studentDevelopment_enabled' => true,
        ]);
        $user1->assignRole($panelUserRole);


        // 2. --- SEKOLAH DEMO ---
        $schoolDemo = School::create([
            'name' => 'SDIT Lahiza Sunnah',
        ]);

        // Akun Khusus: Agus Setiawan (PRO Lifetime)
        $user1 = User::factory()->create([
            'name' => 'Agus Setiawan',
            'email' => 'agussetiawanphy3@gmail.com',
            'password' => Hash::make('12345'),
            'school_id' => $schoolDemo->id,
            'is_pro' => true,
            'pro_expired_at' => $lifetime,
            'is_studentDevelopment_enabled' => true,
        ]);
        $user1->assignRole($panelUserRole);

        // Akun Demo PRO (Lainnya)
        $user2 = User::factory()->create([
            'name' => 'Guru PRO Demo',
            'email' => 'demo@guruku.id',
            'password' => Hash::make('password'),
            'school_id' => $schoolDemo->id,
            'is_pro' => true,
            'pro_expired_at' => $lifetime,
        ]);
        $user2->assignRole($panelUserRole);
    }
}
