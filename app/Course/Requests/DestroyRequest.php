<?php

namespace App\Course\Requests;

use App\Course\Models\CourseMember;
use App\Member\Member;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Http\FormRequest;

class DestroyRequest extends FormRequest
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
        return [];
    }

    public function persist(Member $member, CourseMember $course, NamiSettings $settings): void
    {
        $settings->login()->deleteCourse($member->nami_id, $course->nami_id);

        $course->delete();
    }
}
