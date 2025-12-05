<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
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

    public function update(User $user, Category $category): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, Category $category): bool
    {
        return $this->canManage($user);
    }
}
