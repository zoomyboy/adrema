<?php

namespace App\Payment;

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
            'subscription' => new SubscriptionResource($this->whenLoaded('subscription')),
            'status_name' => $this->status->name,
            'status_id' => $this->status->id,
            'nr' => $this->nr,
            'id' => $this->id,
            'is_accepted' => $this->status->isAccepted(),
        ];
    }
}
