<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTag extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleTagFactory> */
    use HasFactory;

    protected $table = 'article_tags';
    protected $fillable = [
        'article_id',
        'tag_id',
    ];
}
