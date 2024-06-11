<?php

namespace Tests\Feature\Member;

use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Fee;
use App\Gender;
use App\Group;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use App\Member\Member;
use App\Member\Membership;
use App\Nationality;
use App\Payment\Payment;
use App\Payment\Subscription;
use App\Region;
use Carbon\Carbon;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use DatabaseTransactions;

    public function testItShowsSingleMember(): void
    {
        Carbon::setTestNow(Carbon::parse('2006-01-01 15:00:00'));

        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->name('Stamm Beispiel'))
            ->has(Membership::factory()->promise(now())->in('€ LeiterIn', 5, 'Jungpfadfinder', 88)->from('2022-11-19'))
            ->has(InvoicePosition::factory()->for(Invoice::factory())->price(1050)->description('uu'))
            ->for(Gender::factory()->male())
            ->for(Region::factory()->name('NRW'))
            ->postBillKind()
            ->inNami(123)
            ->for(Subscription::factory()->name('Sub')->forFee())
            ->has(CourseMember::factory()->for(Course::factory()->name('  Baustein 2e - Gewalt gegen Kinder und Jugendliche: Vertiefung, Prävention  '))->state(['organizer' => 'DPSG', 'event_name' => 'Wochenende', 'completed_at' => '2022-03-03']), 'courses')
            ->create([
                'birthday' => '1991-04-20',
                'address' => 'Itterstr 3',
                'zip' => '42719',
                'location' => 'Solingen',
                'firstname' => 'Max',
                'lastname' => 'Muster',
                'other_country' => 'other',
                'main_phone' => '+49 212 1266775',
                'mobile_phone' => '+49 212 1266776',
                'work_phone' => '+49 212 1266777',
                'children_phone' => '+49 212 1266778',
                'email' => 'a@b.de',
                'email_parents' => 'b@c.de',
                'fax' => '+49 212 1255674',
                'efz' => '2022-09-20',
                'ps_at' => '2022-04-20',
                'more_ps_at' => '2022-06-02',
                'recertified_at' => '2022-06-13',
                'without_education_at' => '2022-06-03',
                'without_efz_at' => '2022-06-04',
                'has_vk' => true,
                'has_svk' => true,
                'multiply_pv' => true,
                'multiply_more_pv' => true,
                'send_newspaper' => true,
                'joined_at' => '2022-06-11',
                'mitgliedsnr' => 998,
                'lon' => 19.05,
                'lat' => 14.053,
            ]);

        $response = $this->get("/member/{$member->id}");

        $this->assertInertiaHas([
            'birthday_human' => '20.04.1991',
            'age' => 14,
            'group_name' => 'Stamm Beispiel',
            'full_address' => 'Itterstr 3, 42719 Solingen',
            'region' => ['name' => 'NRW'],
            'other_country' => 'other',
            'main_phone' => '+49 212 1266775',
            'mobile_phone' => '+49 212 1266776',
            'work_phone' => '+49 212 1266777',
            'children_phone' => '+49 212 1266778',
            'email' => 'a@b.de',
            'email_parents' => 'b@c.de',
            'fax' => '+49 212 1255674',
            'fullname' => 'Herr Max Muster',
            'efz_human' => '20.09.2022',
            'ps_at_human' => '20.04.2022',
            'more_ps_at_human' => '02.06.2022',
            'without_education_at_human' => '03.06.2022',
            'without_efz_at_human' => '04.06.2022',
            'recertified_at_human' => '13.06.2022',
            'has_vk' => true,
            'has_svk' => true,
            'multiply_pv' => true,
            'multiply_more_pv' => true,
            'has_nami' => true,
            'nami_id' => 123,
            'send_newspaper' => true,
            'joined_at_human' => '11.06.2022',
            'bill_kind_name' => 'Post',
            'mitgliedsnr' => 998,
            'lon' => 19.05,
            'lat' => 14.053,
            'subscription' => [
                'name' => 'Sub',
            ],
        ], $response, 'data');
        $this->assertInertiaHas([
            'activity_name' => '€ LeiterIn',
            'subactivity_name' => 'Jungpfadfinder',
            'id' => $member->memberships->first()->id,
            'human_date' => '19.11.2022',
            'promised_at' => now()->format('Y-m-d'),
        ], $response, 'data.memberships.0');
        $this->assertInertiaHas([
            'organizer' => 'DPSG',
            'event_name' => 'Wochenende',
            'completed_at_human' => '03.03.2022',
            'course' => [
                'name' => '  Baustein 2e - Gewalt gegen Kinder und Jugendliche: Vertiefung, Prävention  ',
                'short_name' => '2e',
            ],
        ], $response, 'data.courses.0');
        $this->assertInertiaHas([
            'description' => 'uu',
            'price_human' => '10,50 €',
            'invoice' => [
                'status' => 'Neu',
            ]
        ], $response, 'data.invoicePositions.0');
    }

    public function testItShowsMinimalSingleMember(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->for(Group::factory())
            ->for(Nationality::factory()->name('deutsch'))
            ->for(Subscription::factory()->forFee())
            ->create(['firstname' => 'Max', 'lastname' => 'Muster']);

        $response = $this->get("/member/{$member->id}");

        $this->assertInertiaHas([
            'region' => ['name' => '-- kein --'],
            'fullname' => 'Max Muster',
            'nationality' => [
                'name' => 'deutsch',
            ],
            'efz_human' => null,
            'ps_at_human' => null,
            'more_ps_at_human' => null,
            'without_education_at_human' => null,
            'without_efz_at_human' => null,
            'has_vk' => false,
            'has_svk' => false,
            'multiply_pv' => false,
            'multiply_more_pv' => false,
        ], $response, 'data');
    }

    public function membershipDataProvider(): Generator
    {
        yield [now()->subMonths(2), null, true];
        yield [now()->subMonths(2), now()->subDay(), false];
        yield [now()->addDays(2), null, false];
    }

    /**
     * @dataProvider membershipDataProvider
     */
    public function testItShowsIfMembershipIsActive(Carbon $from, ?Carbon $to, bool $isActive): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ LeiterIn', 455, 'Pfadfinder', 15)->state(['from' => $from, 'to' => $to]))
            ->create();

        $response = $this->get("/member/{$member->id}");

        $this->assertInertiaHas($isActive, $response, 'data.memberships.0.is_active');
    }
}
