<?php

namespace App\Member;

use App\Activity;
use App\Confession;
use App\Country;
use App\Gender;
use App\Http\Controllers\Controller;
use App\Http\Views\MemberView;
use App\Letter\BillKind;
use App\Nationality;
use App\Payment\Subscription;
use App\Region;
use App\Setting\GeneralSettings;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Zoomyboy\LaravelNami\NamiException;

class MemberController extends Controller
{
    public array $filter = [
        'ausstand' => false,
        'bill_kind' => null,
        'activity_id' => null,
        'subactivity_id' => null,
    ];

    public function index(Request $request, GeneralSettings $settings): Response
    {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglieder');

        $query = [
            'filter' => array_merge(
                $this->filter,
                json_decode($request->query('filter', '{}'), true)
            ),
        ];

        $payload = app(MemberView::class)->index($request, $query['filter']);
        $payload['toolbar'] = [
            ['href' => route('member.create'), 'label' => 'Mitglied anlegen', 'color' => 'primary', 'icon' => 'plus'],
            ['href' => route('allpayment.create'), 'label' => 'Rechnungen erstellen', 'color' => 'primary', 'icon' => 'plus', 'show' => $settings->hasModule('bill')],
            ['href' => route('sendpayment.create'), 'label' => 'Rechnungen versenden', 'color' => 'info', 'icon' => 'envelope', 'show' => $settings->hasModule('bill')],
        ];
        $payload['query'] = $query;
        $payload['billKinds'] = BillKind::pluck('name', 'id');

        return \Inertia::render('member/VIndex', $payload);
    }

    public function create(): Response
    {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglied erstellen');

        $activities = Activity::with('subactivities')->get();

        return \Inertia::render('member/VForm', [
            'activities' => $activities->pluck('name', 'id'),
            'subactivities' => $activities->map(function (Activity $activity) {
                return ['subactivities' => $activity->subactivities()->pluck('name', 'id'), 'id' => $activity->id];
            })->pluck('subactivities', 'id'),
            'billKinds' => BillKind::pluck('name', 'id'),
            'genders' => Gender::pluck('name', 'id'),
            'countries' => Country::pluck('name', 'id'),
            'regions' => Region::where('is_null', false)->pluck('name', 'id'),
            'nationalities' => Nationality::pluck('name', 'id'),
            'confessions' => Confession::where('is_null', false)->pluck('name', 'id'),
            'subscriptions' => Subscription::pluck('name', 'id'),
            'data' => [
                'country_id' => Country::default(),
            ],
            'mode' => 'create',
        ]);
    }

    public function store(MemberRequest $request, NamiSettings $settings): RedirectResponse
    {
        $request->persistCreate($settings);

        return redirect()->route('member.index');
    }

    public function edit(Member $member, Request $request): Response
    {
        session()->put('menu', 'member');
        session()->put('title', "Mitglied {$member->firstname} {$member->lastname} bearbeiten");

        $activities = Activity::with('subactivities')->get();

        return \Inertia::render('member/VForm', [
            'activities' => $activities->pluck('name', 'id'),
            'subactivities' => $activities->map(function ($activity) {
                return ['subactivities' => $activity->subactivities->pluck('name', 'id'), 'id' => $activity->id];
            })->pluck('subactivities', 'id'),
            'billKinds' => BillKind::pluck('name', 'id'),
            'genders' => Gender::pluck('name', 'id'),
            'countries' => Country::pluck('name', 'id'),
            'regions' => Region::where('is_null', false)->pluck('name', 'id'),
            'nationalities' => Nationality::pluck('name', 'id'),
            'confessions' => Confession::where('is_null', false)->pluck('name', 'id'),
            'subscriptions' => Subscription::select('name', 'id')->get(),
            'data' => new MemberResource($member),
            'mode' => 'edit',
            'conflict' => '1' === $request->query('conflict', '0'),
        ]);
    }

    public function update(Member $member, MemberRequest $request): RedirectResponse
    {
        try {
            $request->persistUpdate($member);
        } catch (NamiException $e) {
            if ($e->isConflict()) {
                return redirect()->route('member.edit', ['member' => $member, 'conflict' => '1']);
            } else {
                throw $e;
            }
        }

        return redirect()->route('member.index');
    }

    public function destroy(Member $member): RedirectResponse
    {
        if ($member->has_nami) {
            DeleteJob::dispatch($member, auth()->user());
        }

        $member->delete();

        return redirect()->back();
    }
}
