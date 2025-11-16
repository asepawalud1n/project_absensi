<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'date',
        'status',
        'note',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relationship: Absensi milik satu siswa
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Relationship: Absensi di satu kelas
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    // Relationship: Absensi dicatat oleh user (guru/admin)
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
