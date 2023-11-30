<?php

namespace Database\Factories\Payment;

use App\Fee;
use App\Payment\Payment;
use App\Payment\Status;
use App\Payment\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Tests\RequestFactories\Child;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'nr' => $this->faker->year,
            'subscription_id' => Subscription::factory()->create()->id,
            'status_id' => Status::factory()->create()->id,
            'last_remembered_at' => now(),
        ];
    }

    public function notPaid(): self
    {
        return $this->for(Status::whereName('Nicht bezahlt')->first());
    }

    public function pending(): self
    {
        return $this->for(Status::whereName('Rechnung gestellt')->first())->state(['last_remembered_at' => now()->subYears(2)]);;
    }

    public function paid(): self
    {
        return $this->for(Status::whereName('Rechnung beglichen')->first());
    }

    public function nr(string $nr): self
    {
        return $this->state(['nr' => $nr]);
    }

    /**
     * @param array<int, Child>    $children
     * @param array<string, mixed> $state
     */
    public function subscription(string $name, array $children, array $state = []): self
    {
        return $this->for(
            Subscription::factory()->state(['name' => $name])->for(Fee::factory())->children($children)->state($state)
        );
    }
}
