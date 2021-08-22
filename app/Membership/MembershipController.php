<?php

namespace App\Membership;

use App\Http\Controllers\Controller;
use App\Member\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function store(Member $member, Request $request): RedirectResponse
    {
        $member->memberships()->create($request->input());

        return redirect()->back();
    }
}
