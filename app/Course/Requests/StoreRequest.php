<?php

namespace App\Course\Requests;

use App\Course\Models\Course;
use App\Member\Member;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Http\FormRequest;

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

    public function persist(Member $member, NamiSettings $settings): void
    {
        $course = Course::where('id', $this->input('course_id'))->firstOrFail();

        $payload = collect($this->input())->only(['event_name', 'completed_at', 'organizer'])->merge([
            'course_id' => $course->nami_id,
        ])->toArray();

        $namiId = $settings->login()->createCourse($member->nami_id, $payload);

        $member->courses()->create($this->safe()->collect()->put('nami_id', $namiId)->toArray());
    }
}
