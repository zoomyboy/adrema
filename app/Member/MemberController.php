<?php

namespace App\Member;

use App\Http\Controllers\Controller;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Zoomyboy\LaravelNami\Exceptions\ConflictException;
use Inertia;

class MemberController extends Controller
{
    public function index(Request $request): Response
    {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglieder');

        return Inertia::render('member/VIndex', [
            'data' => MemberResource::collection(FilterScope::fromRequest($request->input('filter', ''))->getQuery()->query(
                fn ($q) => $q
                    ->select('*')
                    ->with(['gender', 'subscription', 'leaderMemberships', 'ageGroupMemberships.subactivity'])
                    ->withPendingPayment()
            )->paginate(15)),
        ]);
    }

    public function create(): Response
    {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglied erstellen');

        return Inertia::render('member/VForm', [
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

        return Inertia::render('member/VForm', [
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
}
