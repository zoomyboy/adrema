<?php

namespace Tests\RequestFactories;

use App\Contribution\ContributionFactory;
use App\Contribution\Documents\ContributionDocument;
use App\Country;
use App\Member\Member;
use Tests\Lib\Queryable;
use Worksome\RequestFactories\RequestFactory;

class ContributionRequestFactory extends RequestFactory
{

    use Queryable;

    public function definition(): array
    {
        $compilers = collect(app(ContributionFactory::class)->compilerSelect())->pluck('class');

        return [
            'country' => Country::factory()->create()->id,
            'dateFrom' => $this->faker->date(),
            'dateUntil' => $this->faker->date(),
            'eventName' => $this->faker->words(3, true),
            'members' => [Member::factory()->defaults()->create()->id],
            'type' => $this->faker->randomElement($compilers),
            'zipLocation' => $this->faker->city,
        ];
    }

    /**
     * @param class-string<ContributionDocument> $type
     */
    public function type(string $type): self
    {
        return $this->state(['type' => $type]);
    }
}
