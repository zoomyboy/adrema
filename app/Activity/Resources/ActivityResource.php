<?php

namespace App\Activity\Resources;

use App\Activity;
use App\Lib\HasMeta;
use App\Resources\SubactivityResource;
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
            'subactivity_model' => [
                'activities' => [$this->id],
                'is_age_group' => false,
                'is_filterable' => false,
                'name' => '',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(): array
    {
        return [
            'subactivities' => SubactivityResource::collectionWithoutMeta(Subactivity::get()),
            'links' => [
                'index' => route('activity.index'),
                'create' => route('activity.create'),
                'membership_masslist' => route('membership.masslist.index'),
                'membership_index' => route('membership.index'),
            ],
        ];
    }
}
