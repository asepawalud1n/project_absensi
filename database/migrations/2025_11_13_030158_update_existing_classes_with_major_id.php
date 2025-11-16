<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint; // Ditambahkan (praktik terbaik)
use Illuminate\Support\Facades\DB;
use App\Models\Major;

return new class extends Migration
{
    public function up(): void
    {
        $defaultMajor = Major::firstOrCreate(
            ['code' => 'UMUM'],
            ['name' => 'Umum', 'description' => 'Jurusan Umum']
        );

   DB::table('classes')
            ->whereNull('major_id')
            ->update(['major_id' => $defaultMajor->id]);
    }

    public function down(): void
    {
    }
};