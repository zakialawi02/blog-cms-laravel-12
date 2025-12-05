<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Newsletter;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsletterPolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return !is_null($user);
    }

    public function delete(User $user, Newsletter $newsletter): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }
}
