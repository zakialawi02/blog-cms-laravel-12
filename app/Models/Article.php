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
        'category_id',
        'user_id',
        'title',
        'content',
        'slug',
        'excerpt',
        'cover',
        'status',
        'is_featured',
        'published_at',
        'views',
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
}
