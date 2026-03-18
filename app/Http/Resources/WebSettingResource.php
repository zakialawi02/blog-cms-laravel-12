<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $value = $this->value;
        if (in_array($this->key, ['app_logo', 'favicon']) && !empty($value)) {
            $value = asset('assets/app_logo/' . $value);
        }

        return [
            'key' => $this->key,
            'value' => $value,
            'type' => $this->type,
        ];
    }
}
