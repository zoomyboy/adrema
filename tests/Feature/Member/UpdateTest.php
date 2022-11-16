<?php

namespace Tests\Feature\Member;

use App\Activity;
use App\Confession;
use App\Country;
use App\Fee;
use App\Group;
use App\Member\Actions\NamiPutMemberAction;
use App\Member\Member;
use App\Nationality;
use App\Payment\Subscription;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Backend\FakeBackend;

class UpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function testItRedirectsToMemberOverview(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = $this->member();
        $this->fakeRequest();
        NamiPutMemberAction::allowToRun();

        $response = $this
            ->from("/member/{$member->id}")
            ->patch("/member/{$member->id}", array_merge($member->getAttributes(), ['has_nami' => true]));

        $response->assertRedirect('/member');
        NamiPutMemberAction::spy()->shouldHaveReceived('handle')->withArgs(fn (Member $memberParam, ?Activity $activityParam, ?Subactivity $subactivityParam) => $memberParam->is($member)
            && null === $activityParam
            && null === $subactivityParam
        )->once();
    }

    public function testItChecksVersion(): void
    {
        $this->login()->loginNami();
        $member = $this->member();
        $member->update(['version' => 43]);
        $this->fakeRequest();

        $response = $this
            ->from("/member/{$member->id}")
            ->patch("/member/{$member->id}", array_merge($member->getAttributes(), ['has_nami' => true, 'firstname' => '::firstname::']));

        $response->assertRedirect("/member/{$member->id}/edit?conflict=1");
    }

    public function testItUpdatesCriminalRecord(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = $this->member(['nami_id' => null]);
        $this->fakeRequest();

        $response = $this
            ->from("/member/{$member->id}")
            ->patch("/member/{$member->id}", array_merge($member->getAttributes(), [
                'ps_at' => '2021-02-01',
                'more_ps_at' => '2021-02-02',
                'has_svk' => true,
                'has_vk' => true,
                'efz' => '2021-02-03',
                'without_education_at' => '2021-02-04',
                'without_efz_at' => '2021-02-05',
                'has_nami' => false,
                'multiply_pv' => true,
                'multiply_more_pv' => true,
            ]));

        $this->assertEquals('2021-02-01', $member->fresh()->ps_at);
        $this->assertEquals('2021-02-02', $member->fresh()->more_ps_at);
        $this->assertTrue($member->fresh()->has_svk);
        $this->assertTrue($member->fresh()->has_vk);
        $this->assertTrue($member->fresh()->multiply_pv);
        $this->assertTrue($member->fresh()->multiply_more_pv);
        $this->assertEquals('2021-02-03', $member->fresh()->efz);
        $this->assertEquals('2021-02-04', $member->fresh()->without_education_at);
        $this->assertEquals('2021-02-05', $member->fresh()->without_efz_at);
    }

    /**
     * @param array<string, string|Activity|null> $overwrites
     */
    private function member(array $overwrites = []): Member
    {
        return Member::factory()
            ->for(Group::factory()->state(['nami_id' => 10]))
            ->for(Confession::factory())
            ->for(Nationality::factory())
            ->for(Subscription::factory()->for(Fee::factory()))
            ->for(Country::factory())
            ->create(['nami_id' => 135, ...$overwrites]);
    }

    private function fakeRequest(): void
    {
        Http::fake(function ($request) {
            if ($request->url() === app(FakeBackend::class)->singleMemberUrl(10, 135) && 'GET' === $request->method()) {
                return Http::response('{ "success": true, "data": {"missingkey": "missingvalue", "kontoverbindung": {"a": "b"} } }', 200);
            }

            if ($request->url() === app(FakeBackend::class)->singleMemberUrl(10, 135) && 'PUT' === $request->method() && 43 === $request['version']) {
                return Http::response('{ "success": false, "message": "Update nicht möglich. Der Datensatz wurde zwischenzeitlich verändert." }', 200);
            }

            if ($request->url() === app(FakeBackend::class)->singleMemberUrl(10, 135) && 'PUT' === $request->method()) {
                return Http::response('{ "success": true, "data": { "version": 44 } }', 200);
            }
        });
    }
}
