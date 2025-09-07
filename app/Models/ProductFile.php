<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ProductFile extends Model
{
    use HasUlids;

    protected $fillable = [
        'product_id',
        'file_name',
        'file_path',
        'download_limit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'download_limit' => 'integer',
        ];
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
