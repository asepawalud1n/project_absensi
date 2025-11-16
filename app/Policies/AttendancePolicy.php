<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        // Admin dan Guru bisa lihat semua absensi
        return in_array($user->role, ['admin', 'guru']);
    }

    public function view(User $user, Attendance $attendance): bool
    {
        // Admin dan Guru bisa lihat detail
        return in_array($user->role, ['admin', 'guru']);
    }

    public function create(User $user): bool
    {
        // Admin dan Guru bisa create
        return in_array($user->role, ['admin', 'guru']);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        // Admin dan Guru bisa update
        return in_array($user->role, ['admin', 'guru']);
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        // Hanya admin yang bisa delete
        return $user->role === 'admin';
    }

    public function deleteAny(User $user): bool
    {
        // Hanya admin yang bisa bulk delete
        return $user->role === 'admin';
    }
}
