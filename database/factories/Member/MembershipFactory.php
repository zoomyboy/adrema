<?php

namespace Database\Factories\Member;

use App\Activity;
use App\Group;
use App\Member\Member;
use App\Member\Membership;
use App\Subactivity;
use Carbon\Carbon;
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
            'promised_at' => null,
        ];
    }

    public function defaults(): self {
        return $this->for(Member::factory()->defaults())->for(Group::factory())->for(Activity::factory())->for(Subactivity::factory());
    }

    public function inNami(int $namiId): self
    {
        return $this->state(['nami_id' => $namiId]);
    }

    public function local(): self
    {
        return $this->state(['nami_id' => null]);
    }

    public function from(string $from): self
    {
        return $this->state(['from' => Carbon::parse($from)]);
    }

    public function inLocal(string $activity, ?string $subactivity = null): self
    {
        $instance = $this->for(Activity::factory()->name($activity));

        if ($subactivity) {
            $instance = $instance->for(Subactivity::factory()->name($subactivity));
        }

        return $instance;
    }

    public function ended(): self
    {
        return $this->state(['to' => now()->subDays(2)]);
    }

    public function in(string $activity, int $activityNamiId, ?string $subactivity = null, ?int $subactivityNamiId = null): self
    {
        $activityModel = Activity::factory()->name($activity)->inNami($activityNamiId)->create();
        $instance = $this->for($activityModel);

        if ($subactivity) {
            $instance = $instance->for(Subactivity::factory()->name($subactivity)->inNami($subactivityNamiId)->hasAttached($activityModel));
        }

        return $instance;
    }

    public function promise(Carbon $value): self
    {
        return $this->state([
            'promised_at' => $value->format('Y-m-d'),
        ]);
    }
}
