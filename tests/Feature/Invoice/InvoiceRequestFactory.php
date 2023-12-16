<?php

namespace Tests\Feature\Invoice;

use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use Worksome\RequestFactories\RequestFactory;

class InvoiceRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'to' => ReceiverRequestFactory::new(),
            'greeting' => 'Hallo Familie',
            'positions' => []
        ];
    }

    public function to(ReceiverRequestFactory $to): self
    {
        return $this->state(['to' => $to]);
    }

    public function status(InvoiceStatus $status): self
    {
        return $this->state(['status' => $status->value]);
    }

    public function position(InvoicePositionRequestFactory $factory): self
    {
        return $this->state(['positions' => [
            $factory->create(),
        ]]);
    }

    public function via(BillKind $via): self
    {
        return $this->state(['via' => $via->value]);
    }
}
