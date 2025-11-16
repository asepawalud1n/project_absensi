<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'nisn',
        'name',
        'gender',
        'class_id',
        'email',
    ];


    // Relationship: Siswa ada di satu kelas
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    // Relationship: Siswa punya banyak absensi
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    // Relationship: Siswa punya akun user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
