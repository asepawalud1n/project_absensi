<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\SchoolClass; // Pastikan model SchoolClass di-import

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    /**
     * Relationship: Major punya banyak kelas
     * Dulu: schoolClasses(). Diubah menjadi: classes()
     */
    public function classes(): HasMany
    {
        // Model SchoolClass yang merujuk ke tabel 'classes'
        return $this->hasMany(SchoolClass::class, 'major_id');
    }
}