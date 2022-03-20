<?php

namespace Database\Factories\Member;

use App\Group;
use App\Member\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Membership>
 */
class MembershipFactory extends Factory
{
    public $model = Membership::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'group_id' => Group::factory()->createOne()->id,
            'from' => now()->subMonths(3),
        ];
    }
}
