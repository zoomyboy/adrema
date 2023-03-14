<?php

namespace Tests\RequestFactories;

use App\Contribution\ContributionFactory;
use App\Contribution\Documents\ContributionDocument;
use App\Country;
use App\Member\Member;
use Worksome\RequestFactories\RequestFactory;

class ContributionRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        $compilers = collect(app(ContributionFactory::class)->compilerSelect())->pluck('class');

        return [
            'country' => $this->faker->randomElement(Country::get())->id,
            'dateFrom' => $this->faker->date(),
            'dateUntil' => $this->faker->date(),
            'eventName' => $this->faker->words(3, true),
            'members' => [$this->faker->randomElement(Member::get())->id],
            'type' => $this->faker->randomElement($compilers),
            'zipLocation' => $this->faker->city,
        ];
    }

    public function toBase64(): string
    {
        return base64_encode(json_encode($this->create()));
    }

    /**
     * @param class-string<ContributionDocument> $type
     */
    public function type(string $type): self
    {
        return $this->state(['type' => $type]);
    }
}
