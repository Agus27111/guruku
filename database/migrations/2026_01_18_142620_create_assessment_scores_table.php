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
        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();

            // Relasi ke induknya (UTS/UAS apa?)
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();

            // Relasi ke siswanya
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            // Nilainya
            $table->decimal('score', 5, 2)->default(0);
            $table->decimal('max_score', 5, 2)->default(100);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_scores');
    }
};
