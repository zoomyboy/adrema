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

    public function handle(Payment $payment): void
    {
        $payment->delete();
    }

    public function asController(Payment $payment): JsonResponse
    {
        $this->startJob($payment);

        return response()->json([]);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        $member = $parameters[0]->member;

        return $jobState
            ->before('Zahlung für ' . $member->fullname . ' wird gelöscht')
            ->after('Zahlung für ' . $member->fullname . ' gelöscht')
            ->failed('Fehler beim Löschen der Zahlung für ' . $member->fullname)
            ->shouldReload(JobChannels::make()->add('member')->add('payment'));
    }
}
