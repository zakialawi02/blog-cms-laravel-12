<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleView extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleViewFactory> */
    use HasFactory;

    protected $table = 'article_views';
    public $timestamps = false;
    protected $fillable = [
        'article_id',
        'viewed_at',
        'ip_address',
        'operating_system',
        'browser',
        'location',
        'code',
    ];
    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
