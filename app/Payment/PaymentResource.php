<?php

namespace App\Payment;

use App\Member\Member;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Payment
 */
class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'subscription_id' => $this->subscription_id,
            'subscription' => new SubscriptionResource($this->whenLoaded('subscription')),
            'status_name' => $this->status->name,
            'status_id' => $this->status->id,
            'nr' => $this->nr,
            'id' => $this->id,
            'is_accepted' => $this->status->isAccepted(),
            'links' => [
                'update' => route('payment.update', ['payment' => $this->getModel()]),
                'destroy' => route('payment.destroy', ['payment' => $this->getModel()]),
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function memberMeta(Member $member): array
    {
        return [
            'statuses' => Status::forSelect(),
            'subscriptions' => Subscription::forSelect(),
            'default' => [
                'nr' => '',
                'subscription_id' => null,
                'status_id' => null
            ],
            'links' => [
                'store' => route('member.payment.store', ['member' => $member]),
            ]
        ];
    }
}
