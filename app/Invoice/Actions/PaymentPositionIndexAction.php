<?php

namespace App\Invoice\Actions;

use App\Invoice\Models\InvoicePosition;
use App\Invoice\Resources\InvoicePositionResource;
use App\Member\Member;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Lorisleiva\Actions\Concerns\AsAction;

class PaymentPositionIndexAction
{
    use AsAction;

    /**
     * @return Collection<int, InvoicePosition>
     */
    public function handle(Member $member): Collection
    {
        return $member->load('invoicePositions.invoice')->invoicePositions;
    }

    public function asController(Member $member): JsonResponse
    {
        return response()->json([
            'data' => InvoicePositionResource::collection($this->handle($member)),
        ]);
    }
}
