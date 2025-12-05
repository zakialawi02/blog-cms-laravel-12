<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'user' => new UserResource($this->whenLoaded('user')),
            'title' => $this->title,
            'content' => $this->content,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'cover' => $this->cover,
            'cover_large' => $this->cover_large,
            'status' => $this->status,
            'is_featured' => (bool) $this->is_featured,
            'meta_title' => $this->meta_title,
            'meta_desc' => $this->meta_desc,
            'meta_keywords' => $this->meta_keywords,
            'published_at' => optional($this->published_at)->toIso8601String(),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'views' => $this->whenLoaded('articleViews', fn() => $this->articleViews->count()),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
