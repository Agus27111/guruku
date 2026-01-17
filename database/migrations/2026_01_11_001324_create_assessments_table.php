<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('subject_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Assessment data
            $table->string('assessment_type'); // daily_test, quiz, midterm, final_exam
            $table->date('assessment_date');

            $table->decimal('score', 5, 2);     // e.g. 85.50
            $table->decimal('max_score', 5, 2)->default(100);
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Optional indexes
            $table->index(['school_id', 'student_id']);
            $table->index('assessment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
