<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'http_method' => $this->http_method,
            'http_path' => $this->http_path,
            'method_color' => $this->method_color,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
