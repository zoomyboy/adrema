<?php

namespace Database\Factories\Invoice\Models;

use App\Invoice\Models\InvoicePosition;
use App\Member\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoicePosition>
 */
class InvoicePositionFactory extends Factory
{
    protected $model = InvoicePosition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'description' => $this->faker->words(4, true),
            'price' => $this->faker->numberBetween(1000, 2000),
        ];
    }

    public function price(int $price): self
    {
        return $this->state(['price' => $price]);
    }

    public function withMember(): self
    {
        return $this->state(['member_id' => Member::factory()->defaults()->create()->id]);
    }
}
