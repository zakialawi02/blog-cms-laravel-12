<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'slug' => $this->slug,
            'articles_count' => $this->when(isset($this->articles_count), $this->articles_count),
        ];
    }
}
