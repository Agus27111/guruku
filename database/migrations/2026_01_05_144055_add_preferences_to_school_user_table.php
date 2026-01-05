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
        Schema::table('school_user', function (Blueprint $table) {
            $table->boolean('is_tahfidz_enabled')->default(true);
            $table->boolean('is_tahsin_enabled')->default(true);
            $table->boolean('is_read_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_user', function (Blueprint $table) {
            //
        });
    }
};
