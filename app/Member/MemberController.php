<?php

namespace App\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Gender;
use App\Fee;

class MemberController extends Controller
{
    public function index(Request $request) {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglieder');

        return \Inertia::render('member/Index', [
            'data' => MemberResource::collection(Member::search($request->query('search', null))->paginate(15))
        ]);
    }

    public function edit(Member $member, Request $request) {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglied bearbeiten');

        return \Inertia::render('member/Edit', [
            'genders' => Gender::where('is_null', false)->get()->pluck('name', 'id'),
            'fees' => Fee::get()->pluck('name', 'id'),
            'data' => new MemberResource($member)
        ]);
    }

    public function update(Member $member, Request $request) {
        $member->update($request->input());

        return redirect()->route('member.index');
    }
}
