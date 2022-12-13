<?php

namespace App\Payment;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SubscriptionChild
 */
class SubscriptionChildResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{amount: int, name: string}
     */
    public function toArray($request)
    {
        return [
            'amount' => $this->amount,
            'name' => $this->name,
        ];
    }
}
