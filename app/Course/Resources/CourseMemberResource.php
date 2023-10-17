<?php

namespace App\Course\Resources;

use App\Course\Models\Course;
use App\Member\Member;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Course\Models\CourseMember
 */
class CourseMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, mixed>
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
            'course' => new CourseResource($this->whenLoaded('course')),
            'links' => [
                'update' => route('course.update', ['course' => $this->getModel()]),
                'destroy' => route('course.destroy', ['course' => $this->getModel()]),
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function memberMeta(Member $member): array
    {
        return [
            'default' => [
                'event_name' => '',
                'completed_at' => null,
                'course_id' => null,
                'organizer' => ''
            ],
            'courses' => Course::forSelect(),
            'links' => [
                'store' => route('member.course.store', ['member' => $member]),
            ]
        ];
    }
}
