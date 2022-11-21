<?php

namespace App\Course\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Course\Models\Course
 */
class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'nami_id' => $this->nami_id,
            'id' => $this->id,
            'short_name' => $this->short_name,
        ];
    }
}
