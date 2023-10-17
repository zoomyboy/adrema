<?php

namespace App\Course\Actions;

use App\Course\Models\Course;
use App\Course\Resources\CourseMemberResource;
use App\Member\Member;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Lorisleiva\Actions\Concerns\AsAction;

class CourseIndexAction
{
    use AsAction;

    /**
     * @return Collection<int, Course>
     */
    public function handle(Member $member): Collection
    {
        return $member->courses()->with('course')->get();
    }

    public function asController(Member $member): AnonymousResourceCollection
    {
        return CourseMemberResource::collection($this->handle($member))
            ->additional([
                'meta' => CourseMemberResource::memberMeta($member),
            ]);
    }
}
