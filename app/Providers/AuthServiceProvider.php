<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Page;
use App\Models\Tag;
use App\Models\Menu;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Newsletter;
use App\Models\WebSetting;
use App\Models\RequestContributor;
use App\Policies\UserPolicy;
use App\Policies\PagePolicy;
use App\Policies\TagPolicy;
use App\Policies\MenuPolicy;
use App\Policies\ArticlePolicy;
use App\Policies\CommentPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\MenuItemPolicy;
use App\Policies\NewsletterPolicy;
use App\Policies\WebSettingPolicy;
use App\Policies\RequestContributorPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
        Category::class => CategoryPolicy::class,
        Tag::class => TagPolicy::class,
        Comment::class => CommentPolicy::class,
        Page::class => PagePolicy::class,
        Menu::class => MenuPolicy::class,
        MenuItem::class => MenuItemPolicy::class,
        Newsletter::class => NewsletterPolicy::class,
        RequestContributor::class => RequestContributorPolicy::class,
        WebSetting::class => WebSettingPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
