<?php

namespace App\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberConfirmController extends Controller
{
    public function __invoke(Request $request, Member $member) {
        $member->update(['confirmed_at' => now()]);

        return redirect()->route('member.index');
    }
}
