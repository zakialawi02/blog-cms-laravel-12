<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Page;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagePolicy
{
    use HandlesAuthorization;

    protected function canManage(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, Page $page): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, Page $page): bool
    {
        return $this->canManage($user);
    }
}
