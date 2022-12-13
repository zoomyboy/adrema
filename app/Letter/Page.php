<?php

namespace App\Letter;

use App\Member\Member;
use App\Payment\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class Page
{
    /**
     * @var Collection<Member>
     */
    private Collection $members;
    public string $familyName;
    public string $singleName;
    public string $address;
    public string $zip;
    public string $location;
    public string $usage;
    public ?string $email;
    /**
     * @var array<string, string>
     */
    public array $positions;

    /**
     * @param Collection<Member> $members
     */
    public function __construct(Collection $members)
    {
        $this->members = $members;
        $this->familyName = $this->members->first()->lastname;
        $this->singleName = $members->first()->lastname;
        $this->address = $members->first()->address;
        $this->zip = $members->first()->zip;
        $this->location = $members->first()->location;
        $this->email = $members->first()->email_parents ?: $members->first()->email;
        $this->positions = $this->getPositions();
        $this->usage = "Mitgliedsbeitrag für {$this->familyName}";
    }

    /**
     * @return array<string, string>
     */
    public function getPositions(): array
    {
        /** @var array<string, string> */
        $result = [];

        foreach ($this->getPayments() as $payment) {
            if ($payment->subscription->split) {
                foreach ($payment->subscription->children as $child) {
                    $result["{$payment->subscription->name} ({$child->name}) {$payment->nr} für {$payment->member->firstname} {$payment->member->lastname}"] = $this->number($child->amount);
                }
            } else {
                $result["{$payment->subscription->name} {$payment->nr} für {$payment->member->firstname} {$payment->member->lastname}"] = $this->number($payment->subscription->getAmount());
            }
        }

        return $result;
    }

    /**
     * @return BaseCollection<int, Payment>
     */
    public function getPayments(): BaseCollection
    {
        return $this->members->pluck('payments')->flatten(1);
    }

    public function number(int $number): string
    {
        return number_format($number / 100, 2, '.', '');
    }
}
