<?php

namespace Tests\Feature\Member;

use App\Activity;
use App\Country;
use App\Fee;
use App\Gender;
use App\Member\Actions\NamiPutMemberAction;
use App\Member\Member;
use App\Nationality;
use App\Payment\Subscription;
use App\Region;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Lib\MergesAttributes;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use DatabaseTransactions;
    use MergesAttributes;

    public function testItCanStoreAMember(): void
    {
        Fee::factory()->create();
        $this->withoutExceptionHandling()->login()->loginNami();
        $country = Country::factory()->create();
        $gender = Gender::factory()->create();
        $region = Region::factory()->create();
        $nationality = Nationality::factory()->create();
        $activity = Activity::factory()->create();
        $subactivity = Subactivity::factory()->create();
        $subscription = Subscription::factory()->create();
        NamiPutMemberAction::allowToRun();

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
                'bill_kind' => 'Post',
            ]));

        $response->assertStatus(302)->assertSessionHasNoErrors();
        $response->assertRedirect('/member');
        $member = Member::firstWhere('firstname', 'Joe');
        $this->assertDatabaseHas('members', [
            'address' => 'Bavert 50',
            'bill_kind' => 'Post',
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
        NamiPutMemberAction::spy()->shouldHaveReceived('handle')->withArgs(fn (Member $memberParam, Activity $activityParam, Subactivity $subactivityParam) => $memberParam->is($member)
            && $activityParam->is($activity)
            && $subactivityParam->is($subactivity)
        )->once();
    }

    public function testItCanStoreAMemberWithoutNami(): void
    {
        Fee::factory()->create();
        $this->withoutExceptionHandling()->login()->loginNami();
        $country = Country::factory()->create();
        $gender = Gender::factory()->create();
        $region = Region::factory()->create();
        $nationality = Nationality::factory()->create();
        $subscription = Subscription::factory()->create();
        $activity = Activity::factory()->create();
        $subactivity = Subactivity::factory()->create();
        NamiPutMemberAction::allowToRun();

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
                'bill_kind' => 'E-Mail',
                'has_nami' => false,
            ]));

        $response->assertStatus(302)->assertSessionHasNoErrors();
        $response->assertRedirect('/member');
        $member = Member::firstWhere('firstname', 'Joe');
        $this->assertDatabaseHas('members', [
            'nami_id' => null,
        ]);
        NamiPutMemberAction::spy()->shouldNotHaveReceived('handle');
    }

    public function testSubscriptionIsRequiredIfFirstActivityIsPaid(): void
    {
        $this->login()->loginNami();
        Fee::factory()->create();
        $country = Country::factory()->create();
        $gender = Gender::factory()->create();
        $region = Region::factory()->create();
        $nationality = Nationality::factory()->create();
        $subscription = Subscription::factory()->create();
        $activity = Activity::factory()->create(['name' => '€ Mitglied']);
        $subactivity = Subactivity::factory()->create();

        $response = $this
            ->from('/member/create')
            ->post('/member', $this->attributes([
                'country_id' => $country->id,
                'gender_id' => $gender->id,
                'region_id' => $region->id,
                'nationality_id' => $nationality->id,
                'first_activity_id' => $activity->id,
                'first_subactivity_id' => $subactivity->id,
                'subscription_id' => null,
                'bill_kind' => 'E-Mail',
            ]));

        $this->assertErrors(['subscription_id' => 'Beitragsart ist erforderlich.'], $response);
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
