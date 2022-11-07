<?php

namespace Database\Factories\Letter;

use App\Letter\BillKind;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<BillKind>
 */
class BillKindFactory extends Factory
{
    public $model = BillKind::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
        ];
    }
}
