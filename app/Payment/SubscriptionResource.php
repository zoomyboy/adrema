<?php

namespace App\Payment;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Subscription
 */
class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'fee_id' => $this->fee_id,
            'fee_name' => $this->fee->name,
            'amount_human' => number_format($this->amount / 100, 2, ',', '.').' €',
            'amount' => $this->amount,
        ];
    }
}
