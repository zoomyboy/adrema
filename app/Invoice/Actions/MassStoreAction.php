<?php

namespace App\Invoice\Actions;

use App\Invoice\Models\Invoice;
use App\Lib\JobMiddleware\JobChannels;
use App\Lib\JobMiddleware\WithJobState;
use App\Lib\Queue\TracksJob;
use App\Member\Member;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class MassStoreAction
{
    use AsAction;
    use TracksJob;

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'year' => 'required|numeric',
        ];
    }

    public function handle(int $year): void
    {
        $memberGroup = Member::payable()->get()
            ->groupBy(fn ($member) => "{$member->bill_kind->value}{$member->lastname}{$member->address}{$member->zip}{$member->location}");
        foreach ($memberGroup as $members) {
            $invoice = Invoice::createForMember($members->first());

            foreach ($members as $member) {
                foreach ($member->subscription->children as $child) {
                    $invoice->positions()->create([
                        'description' => str($child->name)->replace('{name}', $member->firstname . ' ' . $member->lastname)->replace('{year}', $year),
                        'price' => $child->amount,
                        'member_id' => $member->id,
                    ]);
                }
            }
        }
    }

    public function asController(ActionRequest $request): JsonResponse
    {
        $this->startJob($request->year);

        return response()->json([]);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        return $jobState
            ->after('Zahlungen erstellt')
            ->failed('Fehler beim Erstellen von Zahlungen')
            ->shouldReload(JobChannels::make()->add('invoice'));
    }
}
