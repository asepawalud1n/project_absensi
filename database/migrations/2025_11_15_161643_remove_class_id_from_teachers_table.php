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
        Schema::table('teachers', function (Blueprint $table) {
            // ✅ 1. Hapus foreign key constraint dulu
            $table->dropForeign(['class_id']);
            
            // ✅ 2. Baru hapus kolom class_id
            $table->dropColumn('class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Rollback: tambahkan lagi kolom class_id dengan foreign key
            $table->unsignedBigInteger('class_id')->nullable()->after('user_id');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
        });
    }
};
