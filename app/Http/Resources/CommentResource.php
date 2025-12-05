<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'article_id' => $this->article_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'content' => $this->content,
            'is_approved' => (bool) $this->is_approved,
            'parent_id' => $this->parent_id,
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
