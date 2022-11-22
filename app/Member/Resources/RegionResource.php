<?php

namespace App\Member\Resources;

use App\Region;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Region
 */
class RegionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, int|string>
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'nami_id' => $this->nami_id,
            'id' => $this->id,
        ];
    }
}
