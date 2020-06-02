<?php

namespace App\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request) {
        return \Inertia::render('Member/Index', [
            'data' => MemberResource::collection(Member::search($request->query('search', null))->get())
        ]);
    }
}
