<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // TAMBAH EMAIL
            $table->string('email')->nullable()->unique()->after('gender');
            
            // HAPUS KOLOM YANG TIDAK DIPAKAI
            $table->dropColumn(['address', 'phone', 'subject']);
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // HAPUS EMAIL
            $table->dropColumn('email');
            
            // KEMBALIKAN KOLOM LAMA
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
        });
    }
};
