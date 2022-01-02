<?php

namespace App\Course\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Course\Models\CourseMember
 */
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
            'organizer' => $this->organizer,
            'event_name' => $this->event_name,
            'completed_at_human' => Carbon::parse($this->completed_at)->format('d.m.Y'),
            'completed_at' => $this->completed_at,
            'course_name' => $this->course->name,
            'course_id' => $this->course->id,
        ];
    }
}
