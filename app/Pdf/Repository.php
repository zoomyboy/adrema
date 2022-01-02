<?php

namespace App\Pdf;

use App\Member\Member;
use Carbon\Carbon;
use Generator;
use Illuminate\Support\Collection;

abstract class Repository
{

    abstract public function getPayments(Member $member): Collection;

    public Collection $pages;

    public function __construct(Collection $pages)
    {
        $this->pages = $pages;
    }

    public function number(int $number): string
    {
        return number_format($number / 100, 2, '.', '');
    }

    public function getUntil(): Carbon
    {
        return now()->addWeeks(2);
    }

    public function createable(Member $member): bool
    {
        return $this->getPayments($member)->count() !== 0;
    }

    public function allPayments(): Generator
    {
        foreach ($this->pages as $page) {
            foreach ($page as $member) {
                foreach ($this->getPayments($member) as $payment) {
                    yield $payment;
                }
            }
        }
    }

}
