<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function updating(User $user): void
    {
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    }
}