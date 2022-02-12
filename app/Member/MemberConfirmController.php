<?php

namespace App\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemberConfirmController extends Controller
{
    public function __invoke(Request $request, Member $member): RedirectResponse
    {
        $member->update(['confirmed_at' => now()]);

        return redirect()->route('member.index');
    }
}
