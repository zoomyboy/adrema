<?php

namespace Tests\Feature\Invoice;

use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use Worksome\RequestFactories\RequestFactory;

class InvoiceRequestFactory extends RequestFactory
{
    /** @var array<int, InvoicePositionRequestFactory> */
    public $positions = [];

    public function definition(): array
    {
        return [
            'to' => ReceiverRequestFactory::new(),
            'greeting' => 'Hallo Familie',
            'status' => InvoiceStatus::NEW->value,
            'via' => BillKind::EMAIL->value,
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
        $this->positions[] = $factory;

        return $this;
    }

    public function create(array $attributes = []): array
    {
        return parent::create([
            'positions' => array_map(fn ($position) => $position->create(), $this->positions),
            ...$attributes,
        ]);
    }

    public function via(BillKind $via): self
    {
        return $this->state(['via' => $via->value]);
    }
}
