<?php

namespace App\Fileshare\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileshareConnectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'is_active' => $this->type->check(),
        ];
    }
}
