<?php

namespace App\Payment\Actions;

use App\Lib\JobMiddleware\JobChannels;
use App\Lib\JobMiddleware\WithJobState;
use App\Lib\Queue\TracksJob;
use App\Payment\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\In;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class PaymentUpdateAction
{
    use AsAction;
    use TracksJob;

    public function handle(Payment $payment, array $attributes): void
    {
        $payment->update($attributes);
    }

    /**
     * @return array<string, array<int, string|In>>
     */
    public function rules(): array
    {
        return [
            'nr' => 'required',
            'subscription_id' => 'required|exists:subscriptions,id',
            'status_id' => 'required|exists:statuses,id',
        ];
    }

    public function asController(Payment $payment, ActionRequest $request): JsonResponse
    {
        $this->startJob($payment, $request->validated());

        return response()->json([]);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        $member = $parameters[0]->member;

        return $jobState
            ->before('Zahlung für ' . $member->fullname . ' wird aktualisiert')
            ->after('Zahlung für ' . $member->fullname . ' aktualisiert')
            ->failed('Fehler beim Aktualisieren der Zahlung für ' . $member->fullname)
            ->shouldReload(JobChannels::make()->add('member')->add('payment'));
    }
}
