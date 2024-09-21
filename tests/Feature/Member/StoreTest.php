<?php

namespace Tests\Feature\Member;

use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use App\Activity;
use App\Confession;
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
use Zoomyboy\LaravelNami\Fakes\MemberFake;

class StoreTest extends TestCase
{
    use DatabaseTransactions;
    use MergesAttributes;

    public function testItCanStoreAMember(): void
    {
        app(MemberFake::class)->stores(55, 103);
        Fee::factory()->create();
        $this->withoutExceptionHandling()->login()->loginNami();
        $country = Country::factory()->create();
        $gender = Gender::factory()->create();
        $region = Region::factory()->create();
        $nationality = Nationality::factory()->create();
        $activity = Activity::factory()->inNami(89)->create();
        $subactivity = Subactivity::factory()->inNami(90)->create();
        $subscription = Subscription::factory()->forFee()->create();
        $confesstion = Confession::factory()->create(['is_null' => true]);
        PullMemberAction::shouldRun();
        PullMembershipsAction::shouldRun();

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
            ]))->assertSessionHasNoErrors();

        $response->assertRedirect('/member')->assertSessionHasNoErrors();
        $member = Member::firstWhere('firstname', 'Joe');
        $this->assertDatabaseHas('members', [
            'address' => 'Bavert 50',
            'bill_kind' => 'Post',
            'birthday' => '2013-02-19',
            'children_phone' => '+49 176 70512778',
            'country_id' => $country->id,
            'email_parents' => 'osloot@aol.com',
            'firstname' => 'Joe',
            'gender_id' => $gender->id,
            'joined_at' => '2022-08-12',
            'lastname' => 'Muster',
            'letter_address' => null,
            'location' => 'Solingen',
            'main_phone' => '+49 212 337056',
            'mobile_phone' => '+49 176 70512774',
            'nationality_id' => $nationality->id,
            'region_id' => $region->id,
            'send_newspaper' => '1',
            'subscription_id' => $subscription->id,
            'zip' => '42719',
            'fax' => '+49 212 4732223',
            'salutation' => 'Doktor',
            'comment' => 'Lorem bla',
        ]);

        app(MemberFake::class)->assertStored(55, [
            'ersteTaetigkeitId' => 89,
            'ersteUntergliederungId' => 90,
        ]);
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

    public function testItDoesntRequireBirthdayWhenNotInNami(): void
    {
        $this->login()->loginNami();

        $this
            ->post('/member', $this->attributes([
                'nationality_id' => null,
                'birthday' => null,
                'has_nami' => false,
                'address' => null,
                'zip' => null,
                'location' => null,
                'joined_at' => null,
            ]))->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('members', [
            'nationality_id' => null,
            'birthday' => null,
            'address' => null,
            'zip' => null,
            'location' => null,
            'joined_at' => null,
        ]);
    }

    public function testItDoesntNeedSubscription(): void
    {
        $this->login()->loginNami();

        $this
            ->post('/member', $this->attributes([
                'has_nami' => false,
                'subscription_id' => null,
            ]))->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('members', [
            'subscription_id' => null,
        ]);
    }

    public function testItRequiresFields(): void
    {
        $this->login()->loginNami();

        $this
            ->post('/member', $this->attributes([
                'nationality_id' => null,
                'birthday' => '',
                'address' => '',
                'zip' => '',
                'location' => '',
                'joined_at' => '',
            ]))
            ->assertSessionHasErrors(['nationality_id', 'birthday', 'address', 'zip', 'location', 'joined_at']);
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

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
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
            'country_id' => $country->id,
            'nationality_id' => $nationality->id,
            'subscription_id' => $subscription->id,
        ];
    }
}
