<?php

namespace App\Pdf;

use App\Member\Member;
use Carbon\Carbon;
use Generator;

abstract class Repository
{

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
