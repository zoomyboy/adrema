<?php

namespace App\Course\Requests;

use App\Course\Models\Course;
use App\Member\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules()
    {
        return [
            'organizer' => 'required|max:255',
            'event_name' => 'required|max:255',
            'completed_at' => 'required|date',
            'course_id' => 'required|exists:courses,id',
        ];
    }

    public function persist(Member $member): void
    {
        $course = Course::findOrFail($this->input('course_id'));
        $payload = array_merge(
            $this->only(['event_name', 'completed_at', 'course_id', 'organizer']),
            ['course_id' => $course->nami_id],
        );
        $namiId = auth()->user()->api()->createCourse($member->nami_id, $payload);

        $member->courses()->attach(
            $course,
            $this->safe()->collect()->put('nami_id', $namiId)->except(['course_id'])->toArray(),
        );
    }
}
