<?php

namespace App\Course\Requests;

use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Member\Member;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Zoomyboy\LaravelNami\NamiException;

class UpdateRequest extends FormRequest
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

    public function persist(Member $member, CourseMember $course, NamiSettings $settings): void
    {
        $settings->login()->updateCourse(
            $member->nami_id,
            $course->nami_id,
            $this->safe()->merge(['course_id' => Course::find($this->input('course_id'))->nami_id])->toArray()
        );

        $course->update($this->safe()->toArray());
    }
}
