<?php

namespace App\Payment;

use App\Lib\HasMeta;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Subscription
 */
class SubscriptionResource extends JsonResource
{
    use HasMeta;

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
            'id' => $this->id,
            'name' => $this->name,
            'fee_id' => $this->fee_id,
            'fee_name' => $this->fee->name,
            'amount_human' => number_format($this->getAmount() / 100, 2, ',', '.').' €',
            'amount' => $this->getAmount(),
            'split' => $this->split,
            'children' => SubscriptionChildResource::collection($this->whenLoaded('children')),
            'for_promise' => $this->for_promise,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(): array
    {
        return [
            'links' => [
                'index' => route('subscription.index'),
                'create' => route('subscription.create'),
            ],
        ];
    }
}
