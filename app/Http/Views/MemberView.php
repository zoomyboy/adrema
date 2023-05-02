<?php

namespace App\Http\Views;

use App\Activity;
use App\Course\Models\Course;
use App\Member\FilterScope;
use App\Member\Member;
use App\Member\MemberResource;
use App\Payment\Status;
use App\Payment\Subscription;
use App\Region;
use App\Subactivity;
use Illuminate\Http\Request;

class MemberView
{
    public function index(Request $request): array
    {
        $activities = Activity::with('subactivities')->get();
        $filter = FilterScope::fromRequest($request->input('filter', ''));

        return [
            'data' => MemberResource::collection(Member::search($filter->search)->query(fn ($q) => $q->select('*')
                ->withFilter($filter)
                ->with('payments.subscription')->with(['memberships' => fn ($query) => $query->active()])->with('courses')->with('subscription')->with('leaderMemberships')->with('ageGroupMemberships')
                ->withPendingPayment()
                ->ordered()
            )->paginate(15)),
            'filterActivities' => Activity::where('is_filterable', true)->pluck('name', 'id'),
            'filterSubactivities' => Subactivity::where('is_filterable', true)->pluck('name', 'id'),
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
