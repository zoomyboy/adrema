<?php

namespace App\Course\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'organizer' => $this->pivot->organizer,
            'event_name' => $this->pivot->event_name,
            'completed_at_human' => Carbon::parse($this->pivot->completed_at)->format('d.m.Y'),
            'completed_at' => $this->pivot->completed_at,
            'course_name' => $this->name,
        ];
    }
}
