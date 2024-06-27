<?php

namespace App\Fileshare\Resources;

use App\Fileshare\Models\FileshareConnection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FileshareConnection
 */
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
            'type' => get_class($this->type),
            'config' => $this->type->toArray(),
            'id' => $this->id,
        ];
    }
}
