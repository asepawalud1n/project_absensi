<?php

namespace App\Observers;

use App\Models\Teacher;
use App\Models\User;

class TeacherObserver
{

    public function creating(Teacher $teacher): void
    {
        if (!$teacher->user_id && $teacher->email) {
            $user = User::create([
                'name' => $teacher->name,
                'email' => $teacher->email,
                'password' => bcrypt('12345678'),
                'role' => 'guru',
                'email_verified_at' => now(), 
            ]);
            
            $teacher->user_id = $user->id;
        }
    }


    public function updated(Teacher $teacher): void
    {
        if ($teacher->user) {
            $teacher->user->update([
                'name' => $teacher->name,
                'email' => $teacher->email,
            ]);
        }
    }

 
    public function deleted(Teacher $teacher): void
    {
        if ($teacher->user) {
            $teacher->user->delete();
        }
    }
}
