<?php

namespace Tests\RequestFactories;

use App\Activity;
use App\Subactivity;

class MemberStoreRequestFactory extends MemberRequestFactory
{
    public function definition(): array
    {
        $activity = Activity::factory()->inNami(89)->create();
        $subactivity = Subactivity::factory()->inNami(90)->create();

        return [
            ...parent::definition(),
            'first_activity_id' => $activity->id,
            'first_subactivity_id' => $subactivity->id,
        ];
    }
}
