<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
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
            'username' => $this->username,
            'name' => $this->name,
//            'role_ids' => $this->roles->pluck('id'),
//            'roles' => $this->roles,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
