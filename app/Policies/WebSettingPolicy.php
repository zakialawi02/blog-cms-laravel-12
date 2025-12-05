<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WebSetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebSettingPolicy
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

    public function update(User $user, WebSetting $webSetting): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, WebSetting $webSetting): bool
    {
        return $this->canManage($user);
    }
}
