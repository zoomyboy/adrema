<?php

namespace Database\Factories\Invoice\Models;

use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Feature\Invoice\ReceiverRequestFactory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'greeting' => $this->faker->words(4, true),
            'to' => ReceiverRequestFactory::new()->create(),
            'status' => InvoiceStatus::NEW->value,
            'via' => BillKind::POST->value,
            'usage' => $this->faker->words(4, true),
            'mail_email' => $this->faker->safeEmail(),
        ];
    }

    public function to(ReceiverRequestFactory $to): self
    {
        return $this->state(['to' => $to->create()]);
    }

    public function sentAt(Carbon $sentAt): self
    {
        return $this->state(['sent_at' => $sentAt]);
    }

    public function status(InvoiceStatus $status): self
    {
        return $this->state(['status' => $status->value]);
    }

    public function via(BillKind $via): self
    {
        return $this->state(['via' => $via->value]);
    }
}
