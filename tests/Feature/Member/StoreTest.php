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
                'salutation' => 'Doktor',
                'comment' => 'Lorem bla',
            ]));

        $response->assertRedirect('/member')->assertSessionHasNoErrors();
        $member = Member::firstWhere('firstname', 'Joe');
        $this->assertDatabaseHas('members', [
            'address' => 'Bavert 50',
            'bill_kind' => 'Post',
            'birthday' => '2013-02-19',
            'children_phone' => '+49 176 8574112',
            'country_id' => $country->id,
            'email_parents' => 'osloot@aol.com',
            'firstname' => 'Joe',
            'gender_id' => $gender->id,
            'joined_at' => '2022-08-12',
            'lastname' => 'Muster',
            'letter_address' => null,
            'location' => 'Solingen',
            'main_phone' => '+49 212 2334322',
            'mobile_phone' => '+49 176 3033053',
            'nationality_id' => $nationality->id,
            'region_id' => $region->id,
            'send_newspaper' => '1',
            'subscription_id' => $subscription->id,
            'zip' => '42719',
            'fax' => '+49 212 4732223',
            'salutation' => 'Doktor',
            'comment' => 'Lorem bla',
        ]);
        NamiPutMemberAction::spy()->shouldHaveReceived('handle')->withArgs(fn (Member $memberParam, Activity $activityParam, Subactivity $subactivityParam) => $memberParam->is($member)
            && $activityParam->is($activity)
            && $subactivityParam->is($subactivity)
        )->once();
    }

    public function testItCanStoreAMemberWithoutNami(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $activity = Activity::factory()->create();
        $subactivity = Subactivity::factory()->create();

        $response = $this
            ->from('/member/create')
            ->post('/member', $this->attributes([
                'first_activity_id' => $activity->id,
                'first_subactivity_id' => $subactivity->id,
                'has_nami' => false,
            ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('members', [
            'nami_id' => null,
        ]);
        NamiPutMemberAction::spy()->shouldNotHaveReceived('handle');
    }

    public function testItUpdatesPhoneNumber(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();

        $this->post('/member', $this->attributes([
            'has_nami' => false,
            'main_phone' => '02103 4455129',
            'fax' => '02103 4455130',
            'children_phone' => '02103 4455130',
        ]));

        $this->assertDatabaseHas('members', [
            'main_phone' => '+49 2103 4455129',
            'fax' => '+49 2103 4455130',
            'children_phone' => '+49 2103 4455130',
        ]);
    }

    public function testItHasErrorWhenPhoneNumberIsInvalid(): void
    {
        $this->login()->loginNami();

        $response = $this->post('/member', $this->attributes([
            'has_nami' => false,
            'main_phone' => '1111111111111111',
            'mobile_phone' => '1111111111111111',
            'fax' => '1111111111111111',
            'children_phone' => '1111111111111111',
        ]));

        $response->assertSessionHasErrors([
            'main_phone' => 'Telefon (Eltern) ist keine valide Nummer.',
            'mobile_phone' => 'Handy (Eltern) ist keine valide Nummer.',
            'children_phone' => 'Telefon (Kind) ist keine valide Nummer.',
            'fax' => 'Fax ist keine valide Nummer.',
        ]);
    }

    public function testItRequiresFields(): void
    {
        $this->login()->loginNami();

        $this
            ->post('/member', $this->attributes([
                'nationality_id' => null,
            ]))
            ->assertSessionHasErrors(['nationality_id']);
    }

    public function testSubscriptionIsRequiredIfFirstActivityIsPaid(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->name('€ Mitglied')->create();
        $subactivity = Subactivity::factory()->create();

        $this
            ->from('/member/create')
            ->post('/member', $this->attributes([
                'first_activity_id' => $activity->id,
                'first_subactivity_id' => $subactivity->id,
                'subscription_id' => null,
            ]))
            ->assertSessionHasErrors(['subscription_id' => 'Beitragsart ist erforderlich.']);
    }

    public function defaults(): array
    {
        $country = Country::factory()->create();
        $nationality = Nationality::factory()->create();
        $subscription = Subscription::factory()->create();

        return [
            'address' => 'Bavert 50',
            'birthday' => '2013-02-19',
            'children_phone' => '+49 176 8574112',
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
            'main_phone' => '+49 212 2334322',
            'mobile_phone' => '+49 176 3033053',
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
            'country_id' => $country->id,
            'nationality_id' => $nationality->id,
            'subscription_id' => $subscription->id,
        ];
    }
}
