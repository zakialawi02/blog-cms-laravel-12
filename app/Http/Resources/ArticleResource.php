<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'category_id' => $this->category_id ? $this->category_id : "Uncategorized",
            'excerpt' => $this->excerpt ? $this->excerpt : Str::limit(strip_tags($this->content), 250),
            'cover' => $this->cover ? asset("storage/drive/" . $this->user->username . "/img/" . $this->cover) : asset("assets/img/image-placeholder.webp"),
        ];
    }
}
