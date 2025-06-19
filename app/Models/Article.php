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
            $tags = collect(json_decode(request('tags'), true)); // Decode JSON string ke array

            $tagIds = $tags->map(function ($tag) {
                return Tag::firstOrCreate(
                    ['tag_name' => ucwords($tag['value'])], // Mencari berdasarkan tag_name
                    ['slug' => Str::slug($tag['value'])] // Jika tidak ada, buat slug baru
                )->id;
            });

            $this->tags()->sync($tagIds->toArray()); // Sinkronisasi tag dengan tabel pivot
        }
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
