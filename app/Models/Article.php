<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'type',
        'category_id',
        'user_id',
        'title',
        'content',
        'slug',
        'excerpt',
        'cover',
        'cover_large',
        'status',
        'is_featured',
        'meta_title',
        'meta_desc',
        'meta_keywords',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saved(function ($article) {
            $article->syncTags();
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tags', 'article_id', 'tag_id');
    }

    public function articleViews()
    {
        return $this->hasMany(ArticleView::class, 'article_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'article_id');
    }

    public function syncTags()
    {
        // Gunakan helper `request()` untuk mengambil data tags
        if (request()->has('tags')) {
            $raw = request('tags');
            // Support both JSON string (dashboard) and array (API)
            $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
            // If decoded is a flat string array like ["laravel","php"], normalize to [{value: x}]
            if (is_array($decoded) && isset($decoded[0]) && is_string($decoded[0])) {
                $decoded = array_map(fn($v) => ['value' => $v], $decoded);
            }
            $tags = collect($decoded);

            $tagIds = $tags->map(function ($tag) {
                $val = is_string($tag) ? $tag : ($tag['value'] ?? $tag['label'] ?? '');
                if (empty($val)) return null;
                return Tag::firstOrCreate(
                    ['tag_name' => ucwords($val)],
                    ['slug' => Str::slug($val)]
                )->id;
            })->filter();

            $this->tags()->sync($tagIds->toArray());
        }
    }

    /**
     * Explicit tag sync for API — accepts array of strings or [{value/label}] objects.
     */
    public function syncTagsFromArray(?array $tags): void
    {
        if (empty($tags)) return;

        // Normalize flat strings to objects
        $normalized = array_map(function ($t) {
            if (is_string($t)) return ['value' => $t];
            return $t;
        }, $tags);

        $tagIds = collect($normalized)->map(function ($tag) {
            $val = $tag['value'] ?? $tag['label'] ?? '';
            if (empty($val)) return null;
            return Tag::firstOrCreate(
                ['tag_name' => ucwords($val)],
                ['slug' => Str::slug($val)]
            )->id;
        })->filter();

        $this->tags()->sync($tagIds->toArray());
    }

    /**
     * Check if the article is owned by the user or if the user has the superadmin role.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function isOwnedOrSuperadmin(User $user): bool
    {
        return $user->role === 'superadmin' || $user->id === $this->user_id;
    }

    public function isOwnedOrSuperadminOrAdmin(User $user): bool
    {
        return $user->role === 'superadmin' || $user->role === 'admin' || $user->id === $this->user_id;
    }

    /**
     * Check if the article is owned by the user or if the user has the admin role.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function isOwnedOrAdmin(User $user): bool
    {
        return $user->role === 'admin' || $user->id === $this->user_id;
    }

    /**
     * Check if the article is owned by the given user.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function isOwned(User $user): bool
    {
        return $user->id === $this->user_id;
    }

    // Scope
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<', now());
    }

    public function scopeWithCategorySlug($query, $slug)
    {
        return $query->whereHas('category', fn($q) => $q->where('slug', $slug));
    }

    public function scopeWithTagSlug($query, $slug)
    {
        return $query->whereHas('tags', fn($q) => $q->where('slug', $slug));
    }

    public function scopeWithUsername($query, $username)
    {
        return $query->whereHas('user', fn($q) => $q->where('username', $username));
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%$keyword%")
                ->orWhere('content', 'like', "%$keyword%")
                ->orWhere('excerpt', 'like', "%$keyword%")
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%$keyword%")
                    ->orWhere('username', 'like', "%$keyword%"));
        });
    }
}
