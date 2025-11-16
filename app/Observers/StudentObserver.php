<?php

namespace App\Observers;

use App\Models\Student;
use App\Models\User;

class StudentObserver
{

    public function creating(Student $student): void
    {
        if (!$student->user_id && $student->email) {
            $user = User::create([
                'name' => $student->name,
                'email' => $student->email,
                'password' => bcrypt('12345678'),
                'role' => 'siswa',
                'email_verified_at' => now(),
            ]);
            
            $student->user_id = $user->id;
        }
    }


    public function updated(Student $student): void
    {
        if ($student->user) {
            $student->user->update([
                'name' => $student->name,
                'email' => $student->email,
            ]);
        }
    }

    public function deleted(Student $student): void
    {
        if ($student->user) {
            $student->user->delete();
        }
    }
}
