<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
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

    public function update(User $user, Tag $tag): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $this->canManage($user);
    }
}
