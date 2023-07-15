<?php

namespace App\Member;

use App\Country;
use App\Http\Controllers\Controller;
use App\Maildispatcher\Actions\ResyncAction;
use App\Setting\GeneralSettings;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Zoomyboy\LaravelNami\Exceptions\ConflictException;

class MemberController extends Controller
{
    public function index(Request $request, GeneralSettings $settings): Response
    {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglieder');
        $filter = FilterScope::fromRequest($request->input('filter', ''));

        return \Inertia::render('member/VIndex', [
            'data' => MemberResource::collection(Member::search($filter->search)->query(fn ($q) => $q->select('*')
                ->withFilter($filter)
                ->with('payments.subscription')->with(['memberships' => fn ($query) => $query->active()])->with('courses')->with('subscription')->with('leaderMemberships')->with('ageGroupMemberships')
                ->withPendingPayment()
                ->ordered()
            )->paginate(15)),
        ]);
    }

    public function create(): Response
    {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglied erstellen');

        return \Inertia::render('member/VForm', [
            'data' => [
                'country_id' => Country::default(),
                'efz' => null,
                'ps_at' => null,
                'without_education_at' => null,
                'without_efz_at' => null,
                'more_ps_at' => null,
                'joined_at' => now()->format('Y-m-d'),
            ],
            'mode' => 'create',
            'meta' => MemberResource::meta(),
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

        return \Inertia::render('member/VForm', [
            'data' => new MemberResource($member),
            'mode' => 'edit',
            'conflict' => '1' === $request->query('conflict', '0'),
            'meta' => MemberResource::meta(),
        ]);
    }

    public function update(Member $member, MemberRequest $request): RedirectResponse
    {
        try {
            $request->persistUpdate($member);
        } catch (ConflictException $e) {
            return redirect()->route('member.edit', ['member' => $member, 'conflict' => '1']);
        }

        return redirect()->route('member.index');
    }

    public function destroy(Member $member): RedirectResponse
    {
        if ($member->nami_id) {
            DeleteJob::dispatch($member->nami_id);
        }

        $member->delete();
        ResyncAction::dispatch();

        return redirect()->back();
    }
}
