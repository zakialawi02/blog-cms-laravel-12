<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    protected $table = 'web_settings';

    protected $fillable = [
        'web_name',
        'tagline',
        'description',
        'keywords',
        'app_logo',
        'favicon',
        'email',
        'link_fb',
        'link_ig',
        'link_tiktok',
        'link_youtube',
        'link_twitter',
        'link_linkedin',
        'link_github',
    ];
}
