<?php

namespace Tests\RequestFactories;

use App\Country;
use App\Nationality;
use App\Payment\Subscription;
use Worksome\RequestFactories\RequestFactory;

class MemberRequestFactory extends RequestFactory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = Country::factory()->create();
        $nationality = Nationality::factory()->create();
        $subscription = Subscription::factory()->forFee()->create();

        return [
            'address' => 'Bavert 50',
            'birthday' => '2013-02-19',
            'children_phone' => '+49 176 70512778',
            'efz' => '',
            'email' => '',
            'email_parents' => 'osloot@aol.com',
            'fax' => '+49 212 4732223',
            'firstname' => 'Joe',
            'further_address' => '',
            'has_nami' => true,
            'has_svk' => false,
            'has_vk' => false,
            'joined_at' => '2022-08-12',
            'lastname' => 'Muster',
            'letter_address' => '',
            'location' => 'Solingen',
            'main_phone' => '+49 212 337056',
            'mobile_phone' => '+49 176 70512774',
            'more_ps_at' => '',
            'multiply_more_pv' => false,
            'multiply_pv' => false,
            'other_country' => '',
            'ps_at' => '',
            'send_newspaper' => true,
            'without_education_at' => '',
            'without_efz_at' => '',
            'work_phone' => '',
            'zip' => '42719',
            'bank_account' => [
                'iban' => '',
                'bic' => '',
            ],
            'country_id' => $country->id,
            'nationality_id' => $nationality->id,
            'subscription_id' => $subscription->id,
        ];
    }
}
