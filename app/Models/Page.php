<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'id',
        'title',
        'description',
        'content',
        'slug',
        'isFullWidth',
    ];

    /**
     * The slugs of pages that should not be deleted.
     *
     * @var array
     */
    protected static $nonDeletableSlugs = ['terms', 'privacy', 'contact'];

    /**
     * Determine if the page can be deleted.
     *
     * This creates a virtual attribute, e.g., $page->is_deletable
     */
    protected function isDeletable(): Attribute
    {
        return Attribute::make(
            get: fn() => !in_array($this->slug, self::$nonDeletableSlugs),
        );
    }
}
