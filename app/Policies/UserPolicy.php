<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    protected function canManage(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, User $model): bool
    {
        return $this->canManage($user) || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, User $model): bool
    {
        return $this->canManage($user) || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $this->canManage($user) && $user->id !== $model->id;
    }
}
