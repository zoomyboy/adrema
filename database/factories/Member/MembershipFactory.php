<?php

namespace Database\Factories\Member;

use App\Activity;
use App\Group;
use App\Member\Membership;
use App\Subactivity;
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

    public function inNami(int $namiId): self
    {
        return $this->state(['nami_id' => $namiId]);
    }

    public function in(string $activity, int $activityNamiId, ?string $subactivity = null, ?int $subactivityNamiId = null): self
    {
        $instance = $this->for(Activity::factory()->name($activity)->inNami($activityNamiId));

        if ($subactivity) {
            $instance = $instance->for(Subactivity::factory()->name($subactivity)->inNami($subactivityNamiId));
        }

        return $instance;
    }
}
