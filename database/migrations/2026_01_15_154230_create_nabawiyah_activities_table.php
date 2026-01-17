<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nabawiyah_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();

            // Nama Kegiatan / Peristiwa (Bisa diisi manual oleh guru) [cite: 1, 3]
            $table->string('activity_name');

            // --- KELOMPOK INTROVERT (AS-SIRR / السر) [cite: 4] ---
            // Sub-Kategori: Jiwa (Himmah, Ihsan, dll) [cite: 10]
            $table->boolean('pilar_himmah')->default(false);    // Cita-cita tinggi [cite: 10]
            $table->boolean('pilar_ihsaan')->default(false);    // Perfeksionis [cite: 10]
            $table->boolean('pilar_izzah')->default(false);     // Harga diri [cite: 11]
            $table->boolean('pilar_waqaar')->default(false);    // Wibawa [cite: 12]
            $table->boolean('pilar_azimah')->default(false);    // Tekad [cite: 13]
            $table->boolean('pilar_nasyaath')->default(false);  // Semangat [cite: 14]
            $table->boolean('pilar_firaasah')->default(false);  // Firasat [cite: 15]
            $table->boolean('pilar_husnuzhan')->default(false); // Prasangka baik [cite: 17]

            // Sub-Kategori: Akal (Dzakaa, Hikmah, dll) [cite: 19, 20]
            $table->boolean('pilar_dzakaa')->default(false);    // Cerdas [cite: 19]
            $table->boolean('pilar_hikmah')->default(false);    // Hikmah [cite: 20]
            $table->boolean('pilar_kitmaan')->default(false);   // Rahasia (Kitmanus Sirr) [cite: 44]
            $table->boolean('pilar_satr')->default(false);      // Menutup aib [cite: 45]

            // Sub-Kategori: Perasaan (Shidq, Iffah, dll) [cite: 21, 22]
            $table->boolean('pilar_shidq')->default(false);     // Jujur [cite: 21]
            $table->boolean('pilar_iffah')->default(false);     // Jaga diri [cite: 22]
            $table->boolean('pilar_shamt')->default(false);     // Diam [cite: 23]
            $table->boolean('pilar_hayaa')->default(false);     // Malu [cite: 24]
            $table->boolean('pilar_qanaah')->default(false);    // Sederhana [cite: 25]
            $table->boolean('pilar_anaah')->default(false);     // Tidak tergesa [cite: 47]
            $table->boolean('pilar_hilm')->default(false);      // Santun [cite: 48]
            $table->boolean('pilar_tawaadhu')->default(false);  // Rendah hati [cite: 49]
            $table->boolean('pilar_shabr')->default(false);     // Sabar [cite: 50]

            // --- KELOMPOK EXTROVERT (AL-GHALAYAH / الغلاية) [cite: 9] ---
            // Sub-Kategori: Mempengaruhi (Syajaa'ah, Ghairah, dll) [cite: 52, 53]
            $table->boolean('pilar_syajaaah')->default(false);   // Berani [cite: 52]
            $table->boolean('pilar_ghairah')->default(false);    // Cemburu [cite: 53]
            $table->boolean('pilar_munaafasah')->default(false); // Kompetisi [cite: 54]
            $table->boolean('pilar_nashiihah')->default(false);  // Nasehat [cite: 55]
            $table->boolean('pilar_fashaahah')->default(false);  // Fasih bicara [cite: 56]

            // Sub-Kategori: Kerjasama (Nashrah, Sakhaa, dll) [cite: 57, 58]
            $table->boolean('pilar_nashrah')->default(false);    // Menolong [cite: 57]
            $table->boolean('pilar_sakhaa')->default(false);     // Dermawan [cite: 58]
            $table->boolean('pilar_taawun')->default(false);     // Kerjasama [cite: 59]
            $table->boolean('pilar_ulfah')->default(false);      // Bersatu [cite: 60]
            $table->boolean('pilar_adaalah')->default(false);    // Adil [cite: 61]
            $table->boolean('pilar_wafaa')->default(false);      // Tepat janji [cite: 62]

            // Sub-Kategori: Melayani (Muzah, Rahmah, dll) [cite: 43, 63]
            $table->boolean('pilar_muzaah')->default(false);     // Canda [cite: 63]
            $table->boolean('pilar_basyaasyah')->default(false); // Berseri-seri [cite: 64]
            $table->boolean('pilar_rifq')->default(false);       // Lemah lembut [cite: 65]
            $table->boolean('pilar_rahmah')->default(false);     // Belas kasih [cite: 66]
            $table->boolean('pilar_mahabbah')->default(false);   // Penuh cinta [cite: 67]
            $table->boolean('pilar_iitsaar')->default(false);    // Mendahulukan orang lain [cite: 67]
            $table->boolean('pilar_amaanah')->default(false);    // Tanggung jawab [cite: 46]

            $table->text('description')->nullable(); 
            $table->string('image')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nabawiyah_activities');
    }
};
