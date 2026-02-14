<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiArticleGeneration extends Model
{
    protected $fillable = [
        'user_id',
        'topic',
        'language',
        'model',
        'provider',
        'status',
        'result',
        'error_message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
