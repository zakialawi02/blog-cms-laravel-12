<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'link' => $this->link,
            'parent' => $this->parent,
            'sort' => $this->sort,
            'class' => $this->class,
            'menu' => $this->menu,
            'depth' => $this->depth,
            'children' => MenuItemResource::collection($this->whenLoaded('children')),
        ];
    }
}
