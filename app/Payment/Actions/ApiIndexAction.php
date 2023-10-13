<?php

namespace App\Payment\Actions;

use App\Member\Member;
use App\Payment\Payment;
use App\Payment\PaymentResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Lorisleiva\Actions\Concerns\AsAction;

class ApiIndexAction
{
    use AsAction;

    /**
     * @return Collection<int, Payment>
     */
    public function handle(Member $member): Collection
    {
        return $member->payments()->with('subscription')->get();
    }

    public function asController(Member $member): AnonymousResourceCollection
    {
        return PaymentResource::collection($this->handle($member))
            ->additional([
                'meta' => PaymentResource::memberMeta($member),
            ]);
    }
}
