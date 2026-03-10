<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Same as ArticleResource but WITHOUT the 'content' property.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt ?: \Illuminate\Support\Str::words(strip_tags($this->content), 150),
            'cover' => $this->cover ? asset($this->cover) : null,
            'cover_large' => $this->cover_large ? asset($this->cover_large) : null,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'category' => $this->category->category,
                    'slug' => $this->category->slug,
                ];
            }),
            'author' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'username' => $this->user->username,
                    'avatar' => $this->user->profile_photo_url,
                ];
            }),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'meta_title' => $this->meta_title,
            'meta_desc' => $this->meta_desc,
            'meta_keywords' => $this->meta_keywords,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
