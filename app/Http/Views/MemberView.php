<?php

namespace App\Http\Views;

use App\Activity;
use App\Course\Models\Course;
use App\Member\Member;
use App\Member\MemberResource;
use App\Payment\Status;
use App\Payment\Subscription;
use App\Region;
use App\Subactivity;
use Illuminate\Http\Request;

class MemberView
{
    public function index(Request $request, array $filter): array
    {
        $activities = Activity::with('subactivities')->get();

        return [
            'data' => MemberResource::collection(Member::select('*')
                ->filter($filter)->search($request->query('search', null))
                ->with('billKind')->with('payments.subscription')->with('memberships')->with('courses')->with('subscription')->with('leaderMemberships')->with('ageGroupMemberships')
                ->withIsConfirmed()->withPendingPayment()
                ->orderByRaw('lastname, firstname')
                ->paginate(15)
            ),
            'filterActivities' => Activity::where('is_filterable', true)->pluck('name', 'id'),
            'filterSubactivities' => Subactivity::where('is_filterable', true)->pluck('name', 'id'),
            'toolbar' => [['href' => route('member.index'), 'label' => 'Zurück', 'color' => 'primary', 'icon' => 'plus']],
            'paymentDefaults' => ['nr' => date('Y')],
            'subscriptions' => Subscription::pluck('name', 'id'),
            'statuses' => Status::pluck('name', 'id'),
            'regions' => Region::forSelect(),
            'activities' => $activities->pluck('name', 'id'),
            'courses' => Course::pluck('name', 'id'),
            'subactivities' => $activities->map(function (Activity $activity) {
                return ['subactivities' => $activity->subactivities->pluck('name', 'id'), 'id' => $activity->id];
            })->pluck('subactivities', 'id'),
        ];
    }
}
