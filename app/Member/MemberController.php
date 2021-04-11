<?php

namespace App\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request) {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglieder');

        return \Inertia::render('member/Index', [
            'data' => MemberResource::collection(Member::search($request->query('search', null))->paginate(15))
        ]);
    }
}
