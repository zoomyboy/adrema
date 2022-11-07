<?php

namespace Tests\Feature\Member;

use App\Activity;
use App\Country;
use App\Fee;
use App\Gender;
use App\Letter\BillKind;
use App\Member\CreateJob;
use App\Member\Member;
use App\Nationality;
use App\Payment\Subscription;
use App\Region;
use App\Setting\NamiSettings;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\Lib\MergesAttributes;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use DatabaseTransactions;
    use MergesAttributes;

    public function testItCanStoreAMember(): void
    {
        Queue::fake();
        Fee::factory()->create();
        NamiSettings::fake([
            'default_group_id' => 55,
            'password' => 'tt',
        ]);
        $this->withoutExceptionHandling()->login()->loginNami();
        $country = Country::factory()->create();
        $gender = Gender::factory()->create();
        $region = Region::factory()->create();
        $nationality = Nationality::factory()->create();
        $activity = Activity::factory()->create();
        $subactivity = Subactivity::factory()->create();
        $subscription = Subscription::factory()->create();
        $billKind = BillKind::factory()->create();

        $response = $this
            ->from('/member/create')
            ->post('/member', $this->attributes([
                'country_id' => $country->id,
                'gender_id' => $gender->id,
                'region_id' => $region->id,
                'nationality_id' => $nationality->id,
                'first_activity_id' => $activity->id,
                'first_subactivity_id' => $subactivity->id,
                'subscription_id' => $subscription->id,
                'bill_kind_id' => $billKind->id,
            ]));

        $response->assertStatus(302)->assertSessionHasNoErrors();
        $response->assertRedirect('/member');
        $member = Member::firstWhere('firstname', 'Joe');
        Queue::assertPushed(CreateJob::class, fn ($job) => $job->memberId === $member->id);
        $this->assertDatabaseHas('members', [
            'address' => 'Bavert 50',
            'bill_kind_id' => $billKind->id,
            'birthday' => '2013-02-19',
            'children_phone' => '+49 123 44444',
            'country_id' => $country->id,
            'email_parents' => 'osloot@aol.com',
            'firstname' => 'Joe',
            'gender_id' => $gender->id,
            'joined_at' => '2022-08-12',
            'lastname' => 'Muster',
            'letter_address' => null,
            'location' => 'Solingen',
            'main_phone' => '+49 212 2334322',
            'mobile_phone' => '+49 157 53180451',
            'nationality_id' => $nationality->id,
            'region_id' => $region->id,
            'send_newspaper' => '1',
            'subscription_id' => $subscription->id,
            'zip' => '42719',
            'fax' => '+49 666',
        ]);
    }

    public function defaults(): array
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
