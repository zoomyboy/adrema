<?php

namespace App\Group\Resources;

use App\Lib\HasMeta;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{

    use HasMeta;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'inner_name' => $this->inner_name,
            'parent_id' => $this->parent_id,
            'id' => $this->id,
            'level' => $this->level?->value,
        ];
    }

    public static function meta(): array
    {
        return [
            'links' => [
                'bulkstore' => route('group.bulkstore'),
            ]
        ];
    }
}
