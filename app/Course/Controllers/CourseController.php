<?php

namespace App\Course\Controllers;

use App\Course\Requests\StoreRequest;
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
}
