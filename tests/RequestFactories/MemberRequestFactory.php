<?php

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class MemberRequestFactory extends RequestFactory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'address' => 'Bavert 50',
            'birthday' => '2013-02-19',
            'children_phone' => '+49 123 44444',
            'efz' => '',
            'email' => '',
            'email_parents' => 'osloot@aol.com',
            'fax' => '+49 666',
            'firstname' => 'Joe',
            'further_address' => '',
            'has_nami' => true,
            'has_svk' => false,
            'has_vk' => false,
            'joined_at' => '2022-08-12',
            'lastname' => 'Muster',
            'letter_address' => '',
            'location' => 'Solingen',
            'main_phone' => '+49 212 2334322',
            'mobile_phone' => '+49 157 53180451',
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
        ];
    }
}
