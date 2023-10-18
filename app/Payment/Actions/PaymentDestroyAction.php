<?php

namespace App\Payment\Actions;

use App\Lib\JobMiddleware\JobChannels;
use App\Lib\JobMiddleware\WithJobState;
use App\Lib\Queue\TracksJob;
use App\Payment\Payment;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class PaymentDestroyAction
{
    use AsAction;
    use TracksJob;

    public function handle(int $paymentId): void
    {
        Payment::find($paymentId)->delete();
    }

    public function asController(Payment $payment): JsonResponse
    {
        $this->startJob($payment->id, $payment->member->fullname);

        return response()->json([]);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        $memberName = $parameters[1];

        return $jobState
            ->before('Zahlung für ' . $memberName . ' wird gelöscht')
            ->after('Zahlung für ' . $memberName . ' gelöscht')
            ->failed('Fehler beim Löschen der Zahlung für ' . $memberName)
            ->shouldReload(JobChannels::make()->add('member')->add('payment'));
    }
}
