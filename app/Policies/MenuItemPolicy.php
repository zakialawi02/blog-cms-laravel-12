<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MenuItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuItemPolicy
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

    public function update(User $user, MenuItem $menuItem): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, MenuItem $menuItem): bool
    {
        return $this->canManage($user);
    }
}
