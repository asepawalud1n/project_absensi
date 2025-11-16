<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'name',
        'gender',
        'email',
        'user_id',
    ];


    // Relationship: Guru bisa jadi wali kelas
    public function homeroomClass()
    {
        return $this->hasOne(SchoolClass::class, 'homeroom_teacher_id');
    }

    // Relationship: Guru punya akun user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
