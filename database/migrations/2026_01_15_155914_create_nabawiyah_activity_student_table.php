<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nabawiyah_activity_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nabawiyah_activity_id')
                ->constrained('nabawiyah_activities')
                ->cascadeOnDelete()
                ->name('nav_act_id_foreign'); // Nama dipersingkat agar tidak terlalu panjang

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nabawiyah_activity_student');
    }
};
