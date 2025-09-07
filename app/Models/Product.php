<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_name',
        'slug',
        'description',
        'currency',
        'price',
        'discount_price',
        'thumbnail',
        'stock',
        'is_published',
        'sales_count',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sales_count' => 'integer',
            'stock' => 'integer',
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
        ];
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function productFiles()
    {
        return $this->hasMany(ProductFile::class);
    }
}
