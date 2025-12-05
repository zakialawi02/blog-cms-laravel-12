<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RequestContributor;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestContributorPolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return !is_null($user);
    }

    public function update(User $user, RequestContributor $requestContributor): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function delete(User $user, RequestContributor $requestContributor): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }
}
