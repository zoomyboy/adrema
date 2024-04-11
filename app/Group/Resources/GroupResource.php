<?php

namespace App\Group\Resources;

use App\Group;
use App\Group\Enums\Level;
use App\Lib\HasMeta;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Group
 */
class GroupResource extends JsonResource
{

    use HasMeta;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'name' => $request->has('prefer_inner') && $this->inner_name ? $this->inner_name : $this->name,
            'inner_name' => $this->inner_name,
            'parent_id' => $this->parent_id,
            'id' => $this->id,
            'level' => $this->level?->value,
            'children_count' => $this->children_count,
            'links' => [
                'children' => route('api.group', ['group' => $this->id]),
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(): array
    {
        return [
            'links' => [
                'bulkstore' => route('group.bulkstore'),
                'root_path' => route('api.group'),
            ],
            'levels' => Level::forSelect(),
        ];
    }
}
