<?php

namespace App\Form\Resources;

use App\Form\Models\Form;
use App\Lib\HasMeta;
use App\Subactivity;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Form
 */
class FormApiResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'from_human' => $this->from->format('d.m.Y'),
            'to_human' => $this->to->format('d.m.Y'),
            'excerpt' => $this->excerpt,
            'description' => $this->description,
            'config' => $this->config,
            'slug' => $this->slug,
            'dates' => $this->from->equalTo($this->to) ? $this->from->format('d.m.Y') : $this->from->format('d.m.Y') . ' - ' . $this->to->format('d.m.Y'),
            'image' => $this->getMedia('headerImage')->first()->getFullUrl('square'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(): array
    {
        return [
            'base_url' => url(''),
            'agegroups' => Subactivity::remote()->where('is_age_group', true)->get()
                ->map(fn ($subactivity) => ['id' => $subactivity->nami_id, 'name' => $subactivity->name]),
        ];
    }
}
