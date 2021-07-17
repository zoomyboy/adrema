<?php

namespace Database\Factories\Payment;

use App\Fee;
use App\Payment\Payment;
use App\Payment\Status;
use App\Payment\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{

    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            //
        ];
    }

    public function notPaid(): self
    {
        return $this->for(Status::whereName('Nicht bezahlt')->first());
    }

    public function paid(): self
    {
        return $this->for(Status::whereName('Rechnung beglichen')->first());
    }

    public function nr(string $nr): self
    {
        return $this->state(['nr' => $nr]);
    }

    public function subscription(string $name, int $amount): self
    {
        return $this->for(
            Subscription::factory()->state(['name' => $name, 'amount' => $amount])->for(Fee::first()),
        );
    }

}
