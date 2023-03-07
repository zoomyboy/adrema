<?php

namespace App\Resources;

use App\Activity;
use App\Activity\Resources\ActivityResource;
use App\Lib\HasMeta;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Subactivity
 */
class SubactivityResource extends JsonResource
{
    use HasMeta;

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
            'id' => $this->id,
            'name' => $this->name,
            'is_filterable' => $this->is_filterable,
            'activities' => $this->activities->pluck('id')->toArray(),
            'links' => [
                'show' => route('api.subactivity.show', ['subactivity' => $this->getModel()]),
                'update' => route('api.subactivity.update', ['subactivity' => $this->getModel()]),
            ],
        ];
    }

    public static function meta(): array
    {
        return [
            'activities' => ActivityResource::collectionWithoutMeta(Activity::get()),
        ];
    }
}
