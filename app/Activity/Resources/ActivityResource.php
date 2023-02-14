<?php

namespace App\Activity\Resources;

use App\Activity;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Activity
 */
class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{id: int, name: string}
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
        ];
    }
}
