<?php

namespace Tests\RequestFactories;

use App\Activity;
use App\Subactivity;
use Worksome\RequestFactories\RequestFactory;

class MembershipRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [];
    }

    public function in(Activity $activity, ?Subactivity $subactivity = null): self
    {
        return $this->state([
            'activity_id' => $activity->id,
            'subactivity_id' => $subactivity ? $subactivity->id : null,
        ]);
    }

    public function missingAll(): self
    {
        return $this->state([
            'activity_id' => null,
            'subactivity_id' => null,
        ]);
    }

    public function invalidActivity(): self
    {
        return $this->state([
            'activity_id' => 10000,
            'subactivity_id' => null,
        ]);
    }

    public function unmatchingSubactivity(): self
    {
        return $this->state([
            'activity_id' => Activity::factory()->create()->id,
            'subactivity_id' => Subactivity::factory()->create()->id,
        ]);
    }
}
