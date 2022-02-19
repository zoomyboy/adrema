<?php

namespace App\Course\Controllers;

use App\Course\Models\CourseMember;
use App\Course\Requests\DestroyRequest;
use App\Course\Requests\StoreRequest;
use App\Course\Requests\UpdateRequest;
use App\Http\Controllers\Controller;
use App\Member\Member;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function store(Member $member, StoreRequest $request, NamiSettings $settings): RedirectResponse
    {
        $request->persist($member, $settings);

        return redirect()->back()->success('Ausbildung erstellt');
    }

    public function update(Member $member, CourseMember $course, UpdateRequest $request, NamiSettings $settings): RedirectResponse
    {
        $request->persist($member, $course, $settings);

        return redirect()->back()->success('Ausbildung aktualisiert');
    }

    public function destroy(Member $member, CourseMember $course, DestroyRequest $request, NamiSettings $settings): RedirectResponse
    {
        $request->persist($member, $course, $settings);

        return redirect()->back()->success('Ausbildung gelöscht');
    }
}
