<?php

namespace App\Payment\Actions;

use App\Lib\JobMiddleware\JobChannels;
use App\Lib\JobMiddleware\WithJobState;
use App\Lib\Queue\TracksJob;
use App\Member\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\In;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class PaymentStoreAction
{
    use AsAction;
    use TracksJob;

    public function handle(Member $member, array $attributes): void
    {
        $member->createPayment($attributes);
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

    public function asController(Member $member, ActionRequest $request): JsonResponse
    {
        $this->startJob($member, $request->validated());

        return response()->json([]);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        $member = $parameters[0];

        return $jobState
            ->before('Zahlung für ' . $member->fullname . ' wird gespeichert')
            ->after('Zahlung für ' . $member->fullname . ' gespeichert')
            ->failed('Fehler beim Erstellen der Zahlung für ' . $member->fullname)
            ->shouldReload(JobChannels::make()->add('member')->add('payment'));
    }
}
