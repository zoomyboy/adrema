<?php

namespace Database\Factories\Member;

use App\Member\BankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template TModel of \App\Member\BankAccount
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class BankAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = BankAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank_name' => $this->faker->name(),
            'bic' => $this->faker->swiftBicNumber(),
            'iban' => $this->faker->iban('DE'),
            'blz' => $this->faker->name(),
            'person' => $this->faker->name(),
            'account_number' => $this->faker->name(),
        ];
    }

    public function inNami(int $namiId): self
    {
        return $this->state(['nami_id' => $namiId]);
    }
}
