<?php

namespace App\Http\Views;

use App\Member\Member;
use App\Member\MemberResource;
use App\Payment\ActionFactory;
use App\Payment\PaymentResource;
use App\Payment\Status;
use App\Payment\Subscription;
use Illuminate\Http\Request;

class MemberView {
    public function index(Request $request) {
        return [
            'data' => MemberResource::collection(Member::select('*')->search($request->query('search', null))->with('billKind')->withSubscriptionName()->withIsConfirmed()->withPendingPayment()->orderByRaw('lastname, firstname')->paginate(15)),
            'toolbar' => [ ['href' => route('member.index'), 'label' => 'Zurück', 'color' => 'primary', 'icon' => 'plus'] ]
        ];
    }

    public function paymentCreate($member) {
        return $this->additional($member, [
            'model' => [
                'subscription_id' => $member->subscription_id,
                'status_id' => Status::default(),
                'nr' => date('Y'),
            ],
            'links' => [ ['label' => 'Zurück', 'href' => route('member.payment.index', ['member' => $member]) ] ],
            'mode' => 'create',
        ]);
    }

    public function paymentEdit($member, $payment) {
        return $this->additional($member, [
            'model' => new PaymentResource($payment),
            'links' => [ ['label' => 'Zurück', 'href' => route('member.payment.index', ['member' => $member]) ] ],
            'mode' => 'edit',
        ]);
    }

    public function paymentIndex($member) {
        return $this->additional($member, [
            'model' => null,
            'links' => [
                ['icon' => 'plus', 'href' => route('member.payment.create', ['member' => $member]) ],
            ],
            'payment_links' => app(ActionFactory::class)->forMember($member),
            'mode' => 'index',
        ]);
    }

    private function additional($member, $overwrites = []) {
        return (new MemberResource($member->load('payments')))
            ->additional(array_merge([
                'subscriptions' => Subscription::get()->pluck('name', 'id'),
                'statuses' => Status::get()->pluck('name', 'id'),
            ], $overwrites));
    }

}
