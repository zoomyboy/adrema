<?php

namespace App\Http\Views;

use App\Activity;
use App\Course\Models\Course;
use App\Member\Member;
use App\Member\MemberResource;
use App\Payment\ActionFactory;
use App\Payment\Payment;
use App\Payment\PaymentResource;
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
                ->with('billKind')->with('payments')->with('memberships')->with('courses')
                ->withSubscriptionName()->withIsConfirmed()->withPendingPayment()->withAgeGroup()
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

    public function paymentEdit(Member $member, Payment $payment): MemberResource
    {
        return $this->additional($member, [
            'model' => new PaymentResource($payment),
            'links' => [['label' => 'Zurück', 'href' => route('member.payment.index', ['member' => $member])]],
            'mode' => 'edit',
        ]);
    }

    public function paymentIndex(Member $member): MemberResource
    {
        return $this->additional($member, [
            'model' => null,
            'links' => [
                ['icon' => 'plus', 'href' => route('member.payment.create', ['member' => $member])],
            ],
            'payment_links' => app(ActionFactory::class)->forMember($member),
            'mode' => 'index',
        ]);
    }

    private function additional(Member $member, array $overwrites = []): MemberResource
    {
        return (new MemberResource($member->load('payments')))
            ->additional(array_merge([
                'subscriptions' => Subscription::pluck('name', 'id'),
                'statuses' => Status::pluck('name', 'id'),
            ], $overwrites));
    }
}
