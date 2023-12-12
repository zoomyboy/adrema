<?php

namespace Tests\Feature\Invoice;

use App\Member\Member;
use Worksome\RequestFactories\RequestFactory;

class InvoicePositionRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'description' => 'Beitrag Abc',
            'price' => 3250,
        ];
    }

    public function description(string $description): self
    {
        return $this->state(['description' => $description]);
    }

    public function price(int $price): self
    {
        return $this->state(['price' => $price]);
    }

    public function member(Member $member): self
    {
        return $this->state(['member_id' => $member->id]);
    }
}
