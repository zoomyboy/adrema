<?php

namespace Database\Factories;

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
            //
        ];
    }
}
