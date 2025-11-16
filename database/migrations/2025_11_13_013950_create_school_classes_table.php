<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: X-A, XI-B
            $table->string('grade'); // Contoh: 10, 11, 12
            $table->unsignedBigInteger('homeroom_teacher_id')->nullable(); // Tanpa foreign key dulu
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
