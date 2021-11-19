<?php

namespace Database\Factories\Member;

use App\Country;
use App\Fee;
use App\Group;
use App\Member\Member;
use App\Nationality;
use App\Payment\Payment;
use App\Payment\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
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
            : Subscription::factory()->for(Fee::factory())->create();

        return $this
            ->for($country)
            ->for($group)
            ->for($nationality)
            ->for($subscription);
    }

    public function inNami(int $namiId): self
    {
        return $this->state(['nami_id' => $namiId]);
    }

    /**
     * @param array<int, callable> $payments
     */
    public function withPayments(array $payments): self
    {
        return $this->afterCreating(function (Member $model) use ($payments): void {
            foreach ($payments as $paymentClosure) {
                $factory = Payment::factory()->for($model);
                $factory = call_user_func($paymentClosure, $factory);
                $factory->create();
            }
        });
    }

}
