<?php

namespace Database\Factories\Member;

use App\Country;
use App\Gender;
use App\Group;
use App\Invoice\BillKind;
use App\Member\Member;
use App\Nationality;
use App\Payment\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'birthday' => $this->faker->dateTimeBetween('-30 years'),
            'joined_at' => $this->faker->dateTimeBetween('-30 years'),
            'send_newspaper' => $this->faker->boolean,
            'address' => $this->faker->streetAddress,
            'zip' => $this->faker->postcode,
            'location' => $this->faker->city,
            'email' => $this->faker->safeEmail(),
            'recertified_at' => null,
        ];
    }

    public function defaults(): self
    {
        $country = Country::count()
            ? Country::get()->random()
            : Country::factory()->create();
        $group = Group::count()
            ? Group::get()->random()
            : Group::factory()->create();
        $nationality = Nationality::count()
            ? Nationality::get()->random()
            : Nationality::factory()->create();
        $subscription = Subscription::count()
            ? Subscription::get()->random()
            : Subscription::factory()->forFee()->create();

        return $this
            ->for($country)
            ->for($group)
            ->for($nationality)
            ->for($subscription);
    }

    public function postBillKind(): self
    {
        return $this->state([
            'bill_kind' => BillKind::POST,
        ]);
    }

    public function male(): self
    {
        return $this->for(Gender::factory()->male());
    }

    public function female(): self
    {
        return $this->for(Gender::factory()->female());
    }

    public function emailBillKind(): self
    {
        return $this->state([
            'bill_kind' => BillKind::EMAIL,
        ]);
    }

    public function inNami(int $namiId): self
    {
        return $this->state(['nami_id' => $namiId]);
    }

    public function sameFamilyAs(Member $member): self
    {
        return $this->state([
            'firstname' => $member->firstname . 'a',
            'lastname' => $member->lastname,
            'address' => $member->address,
            'zip' => $member->zip,
            'location' => $member->location,
        ]);
    }
}
