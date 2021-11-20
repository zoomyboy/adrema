<?php

namespace App\Course\Controllers;

use App\Course\Models\CourseMember;
use App\Course\Requests\DestroyRequest;
use App\Course\Requests\StoreRequest;
use App\Course\Requests\UpdateRequest;
use App\Http\Controllers\Controller;
use App\Member\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function store(Member $member, StoreRequest $request): RedirectResponse
    {
        $request->persist($member);

        return redirect()->route('member.index');
    }

    public function update(Member $member, CourseMember $course, UpdateRequest $request): RedirectResponse
    {
        $request->persist($member, $course);

        return redirect()->route('member.index');
    }

    public function destroy(Member $member, CourseMember $course, DestroyRequest $request): RedirectResponse
    {
        $request->persist($member, $course);

        return redirect()->route('member.index');
    }
}
