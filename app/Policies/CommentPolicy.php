<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return !is_null($user);
    }

    public function update(User $user, Comment $comment): bool
    {
        return in_array($user->role, ['superadmin', 'admin']) || $comment->user_id === $user->id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return in_array($user->role, ['superadmin', 'admin']) || $comment->user_id === $user->id;
    }
}
