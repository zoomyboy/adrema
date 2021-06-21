<?php

namespace App\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Gender;
use App\Fee;
use App\Region;
use App\Country;
use App\Nationality;
use App\Confession;
use App\Bill\BillKind;

class MemberController extends Controller
{

    public function index(Request $request) {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglieder');

        return \Inertia::render('member/Index', [
            'data' => MemberResource::collection(Member::search($request->query('search', null))->with('billKind')->paginate(15)),
            'toolbar' => [ ['href' => route('member.create'), 'label' => 'Mitglied anlegen', 'color' => 'primary', 'icon' => 'plus'] ],
        ]);
    }

    public function create() {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglied erstellen');

        return \Inertia::render('member/Form', [
            'billKinds' => BillKind::get()->pluck('name', 'id'),
            'genders' => Gender::get()->pluck('name', 'id'),
            'countries' => Country::get()->pluck('name', 'id'),
            'regions' => Region::where('is_null', false)->get()->pluck('name', 'id'),
            'nationalities' => Nationality::get()->pluck('name', 'id'),
            'confessions' => Confession::where('is_null', false)->get()->pluck('name', 'id'),
            'fees' => Fee::get()->pluck('name', 'id'),
            'data' => null,
            'mode' => 'create',
        ]);
    }

    public function store(MemberRequest $request) {
        $request->persistCreate();

        return redirect()->route('member.index');
    }

    public function edit(Member $member, Request $request) {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglied bearbeiten');

        return \Inertia::render('member/Form', [
            'billKinds' => BillKind::get()->pluck('name', 'id'),
            'genders' => Gender::get()->pluck('name', 'id'),
            'countries' => Country::get()->pluck('name', 'id'),
            'regions' => Region::where('is_null', false)->get()->pluck('name', 'id'),
            'nationalities' => Nationality::get()->pluck('name', 'id'),
            'confessions' => Confession::where('is_null', false)->get()->pluck('name', 'id'),
            'fees' => Fee::get()->pluck('name', 'id'),
            'data' => new MemberResource($member),
            'mode' => 'edit',
        ]);
    }

    public function update(Member $member, MemberRequest $request) {
        $request->persistUpdate($member);

        return redirect()->route('member.index');
    }
}
