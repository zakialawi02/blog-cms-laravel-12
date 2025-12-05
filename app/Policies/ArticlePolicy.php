<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Article;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Article $article): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin', 'writer']);
    }

    public function update(User $user, Article $article): bool
    {
        return in_array($user->role, ['superadmin', 'admin']) || $article->isOwned($user);
    }

    public function delete(User $user, Article $article): bool
    {
        return in_array($user->role, ['superadmin', 'admin']) || $article->isOwned($user);
    }
}
