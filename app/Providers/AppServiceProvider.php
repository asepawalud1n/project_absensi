<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Observers\UserObserver;
use App\Observers\TeacherObserver;
use App\Observers\StudentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Observer untuk User (email verification)
        User::observe(UserObserver::class);
        
        // Observer untuk auto-create User saat Teacher/Student dibuat
        Teacher::observe(TeacherObserver::class);
        Student::observe(StudentObserver::class);
    }
}
