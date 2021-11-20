<?php

namespace App\Course\Requests;

use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Member\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Zoomyboy\LaravelNami\NamiException;

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

    public function persist(Member $member, CourseMember $course): void
    {
        try {
            auth()->user()->api()->deleteCourse($member->nami_id, $course->nami_id);
        } catch(NamiException $e) {
            throw ValidationException::withMessages(['id' => 'Unbekannter Fehler']);
        }

        $course->delete();
    }
}
