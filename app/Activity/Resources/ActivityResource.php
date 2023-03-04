<?php

namespace App\Activity\Resources;

use App\Activity;
use App\Http\Views\ActivityFilterScope;
use App\Lib\HasMeta;
use App\Subactivity;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Activity
 */
class ActivityResource extends JsonResource
{
    use HasMeta;

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
            'subactivities' => $this->subactivities->pluck('id')->toArray(),
            'is_filterable' => $this->is_filterable,
            'links' => [
                'edit' => route('activity.edit', ['activity' => $this->getModel()]),
                'update' => route('activity.update', ['activity' => $this->getModel()]),
                'destroy' => route('activity.destroy', ['activity' => $this->getModel()]),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(): array
    {
        return [
            'subactivities' => Subactivity::select('name', 'id')->get(),
            'filter' => ActivityFilterScope::fromRequest(request()->input('filter')),
        ];
    }
}
