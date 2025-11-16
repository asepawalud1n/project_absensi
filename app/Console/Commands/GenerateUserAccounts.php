<?php

namespace App\Console\Commands;

use App\Models\Teacher;
use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateUserAccounts extends Command
{
    protected $signature = 'users:generate';
    protected $description = 'Generate user accounts for existing teachers and students';

    public function handle()
    {
        $this->info('Generating user accounts...');

        $teachers = Teacher::whereNull('user_id')->get();
        $teacherCount = 0;

        foreach ($teachers as $teacher) {
            $user = User::create([
                'name' => $teacher->name,
                'email' => $teacher->email,
                'password' => Hash::make('12345678'),
                'role' => 'guru',
                'email_verified_at' => now(),
            ]);

            $teacher->update(['user_id' => $user->id]);
            $teacherCount++;
        }

        $this->info("Created {$teacherCount} accounts for teachers");

        $students = Student::whereNull('user_id')->get();
        $studentCount = 0;

        foreach ($students as $student) {
            $user = User::create([
                'name' => $student->name,
                'email' => $student->email ?? $student->nis . '@sekolah.id',
                'password' => Hash::make('12345678'),
                'role' => 'siswa',
                'email_verified_at' => now(),
            ]);

            $student->update(['user_id' => $user->id]);
            $studentCount++;
        }

        $this->info("Created {$studentCount} accounts for students");
        $this->info(' All user accounts generated successfully!');
    }
}
