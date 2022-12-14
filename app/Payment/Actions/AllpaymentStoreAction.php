<?php

namespace App\Payment\Actions;

use App\Member\Member;
use App\Member\Membership;
use App\Payment\Status;
use App\Payment\Subscription;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class AllpaymentStoreAction
{
    use AsAction;

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'year' => 'required|numeric',
            'for_promise' => 'present|boolean',
        ];
    }

    public function handle(int $year, bool $forPromise): void
    {
        foreach (Member::payable()->whereNoPayment($year)->get() as $member) {
            $member->createPayment([
                'nr' => $year,
                'subscription_id' => $member->subscription_id,
                'status_id' => Status::default(),
            ]);

            if (!$forPromise) {
                continue;
            }

            $this->createPaymentsForPromise($member, $year);
        }
    }

    private function createPaymentsForPromise(Member $member, int $year): void
    {
        $subscription = Subscription::firstWhere('for_promise', true);

        if (is_null($subscription)) {
            return;
        }

        foreach ($this->promisedMemberships($member, $year) as $membership) {
            $attributes = [
                'nr' => $membership->subactivity->name.' '.$membership->promised_at->year,
                'subscription_id' => $subscription->id,
            ];

            if (!$member->payments()->where($attributes)->exists()) {
                $member->createPayment([
                    ...$attributes,
                    'status_id' => Status::default(),
                ]);
            }
        }
    }

    /**
     * @return Collection<Membership>
     */
    public function promisedMemberships(Member $member, int $year): Collection
    {
        return $member->memberships()->whereNotNull('promised_at')->whereYear('promised_at', now()->year($year)->subYear())->get();
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $this->handle($request->year, $request->for_promise);

        return redirect()->back()->success('Zahlungen erstellt');
    }
}
