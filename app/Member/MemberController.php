<?php

namespace App\Member;

use App\Activity;
use App\Bill\BillKind;
use App\Confession;
use App\Country;
use App\Gender;
use App\Group;
use App\Http\Controllers\Controller;
use App\Http\Views\MemberView;
use App\Member\DeleteJob;
use App\Nationality;
use App\Payment\Subscription;
use App\Region;
use Illuminate\Http\Request;
use Inertia\Response;

class MemberController extends Controller
{

    public function index(Request $request): Response {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglieder');

        $payload = app(MemberView::class)->index($request);
        $payload['toolbar'] = [
            ['href' => route('member.create'), 'label' => 'Mitglied anlegen', 'color' => 'primary', 'icon' => 'plus'],
            ['href' => route('allpayment.create'), 'label' => 'Rechnungen erstellen', 'color' => 'primary', 'icon' => 'plus'],
            ['href' => route('sendpayment.create'), 'label' => 'Rechnungen versenden', 'color' => 'info', 'icon' => 'envelope'],
        ];

        return \Inertia::render('member/Index', $payload);
    }

    public function create(): Response {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglied erstellen');

        $activities = Activity::with('subactivities')->get();

        return \Inertia::render('member/Form', [
            'activities' => $activities->pluck('name', 'id'),
            'subactivities' => $activities->map(function(Activity $activity) {
                return ['subactivities' => $activity->subactivities->pluck('name', 'id'), 'id' => $activity->id];
            })->pluck('subactivities', 'id'),
            'billKinds' => BillKind::get()->pluck('name', 'id'),
            'genders' => Gender::get()->pluck('name', 'id'),
            'countries' => Country::get()->pluck('name', 'id'),
            'regions' => Region::where('is_null', false)->get()->pluck('name', 'id'),
            'nationalities' => Nationality::get()->pluck('name', 'id'),
            'confessions' => Confession::where('is_null', false)->get()->pluck('name', 'id'),
            'subscriptions' => Subscription::get()->pluck('name', 'id'),
            'data' => [
                'country_id' => Country::default()
            ],
            'mode' => 'create',
        ]);
    }

    public function store(MemberRequest $request) {
        $request->persistCreate();

        return redirect()->route('member.index');
    }

    public function edit(Member $member, Request $request) {
        session()->put('menu', 'member');
        session()->put('title', "Mitglied {$member->firstname} {$member->lastname} bearbeiten");

        $activities = Activity::with('subactivities')->get();

        return \Inertia::render('member/Form', [
            'activities' => $activities->pluck('name', 'id'),
            'subactivities' => $activities->map(function($activity) {
                return ['subactivities' => $activity->subactivities->pluck('name', 'id'), 'id' => $activity->id];
            })->pluck('subactivities', 'id'),
            'billKinds' => BillKind::get()->pluck('name', 'id'),
            'genders' => Gender::get()->pluck('name', 'id'),
            'countries' => Country::get()->pluck('name', 'id'),
            'regions' => Region::where('is_null', false)->get()->pluck('name', 'id'),
            'nationalities' => Nationality::get()->pluck('name', 'id'),
            'confessions' => Confession::where('is_null', false)->get()->pluck('name', 'id'),
            'subscriptions' => Subscription::get()->pluck('name', 'id'),
            'data' => new MemberResource($member),
            'mode' => 'edit',
        ]);
    }

    public function update(Member $member, MemberRequest $request) {
        $request->persistUpdate($member);

        return redirect()->route('member.index');
    }

    public function destroy(Member $member) {
        if ($member->has_nami) {
            DeleteJob::dispatch($member, auth()->user());
        }

        $member->delete();

        return redirect()->back();
    }
}
