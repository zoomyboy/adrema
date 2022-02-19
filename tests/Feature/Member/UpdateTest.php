<?php

namespace Tests\Feature\Member;

use App\Confession;
use App\Country;
use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Fee;
use App\Group;
use App\Member\Member;
use App\Nationality;
use App\Payment\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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

        $response = $this
            ->from("/member/{$member->id}")
            ->patch("/member/{$member->id}", array_merge($member->getAttributes(), ['has_nami' => true]));

        $response->assertRedirect('/member');
    }

    public function testItHasPutRequest(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = $this->member();
        $this->fakeRequest();

        $response = $this
            ->from("/member/{$member->id}")
            ->patch("/member/{$member->id}", array_merge($member->getAttributes(), ['has_nami' => true, 'firstname' => '::firstname::']));

        Http::assertSent(fn ($request) => $request->method() === 'PUT'
            && $request['vorname'] === '::firstname::'
        );
    }

    public function testItMergesExistingData(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = $this->member();
        $this->fakeRequest();

        $response = $this
            ->from("/member/{$member->id}")
            ->patch("/member/{$member->id}", array_merge($member->getAttributes(), ['has_nami' => true, 'firstname' => '::firstname::']));

        Http::assertSent(fn ($request) => $request->method() === 'PUT'
            && $request['kontoverbindung'] === '{"a":"b"}'
            && $request['missingkey'] === 'missingvalue'
            && $request['vorname'] === '::firstname::'
        );
    }

    public function testItUpdatesVersion(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = $this->member();
        $this->fakeRequest();

        $response = $this
            ->from("/member/{$member->id}")
            ->patch("/member/{$member->id}", array_merge($member->getAttributes(), ['has_nami' => true]));

        $this->assertEquals(44, $member->fresh()->version);
    }

    private function member(): Member
    {
        return Member::factory()
            ->for(Group::factory()->state(['nami_id' => 10]))
            ->for(Confession::factory())
            ->for(Nationality::factory())
            ->for(Subscription::factory()->for(Fee::factory()))
            ->for(Country::factory())
            ->create(['nami_id' => 135]);       
    }

    private function fakeRequest(): void
    {
        Http::fake(function ($request) {
            if ($request->url() === app(FakeBackend::class)->singleMemberUrl(10, 135) && $request->method() === 'GET') {
                return Http::response('{ "success": true, "data": {"missingkey": "missingvalue", "kontoverbindung": {"a": "b"} } }', 200);
            }

            if ($request->url() === app(FakeBackend::class)->singleMemberUrl(10, 135) && $request->method() === 'PUT') {
                return Http::response('{ "success": true, "data": { "version": 44 } }', 200);
            }
        });
    }

}
