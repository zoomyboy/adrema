<?php

namespace Tests\Feature\Member;

use App\Member\Member;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Zoomyboy\Osm\FillCoordsJob;

class GeolocationTest extends TestCase
{
    public function testItFiresGeolocationJob(): void
    {
        Queue::fake();
        Member::enableGeolocation();
        $member = Member::factory()->defaults()->create();

        Queue::assertPushed(FillCoordsJob::class, fn ($job) => $job->model->is($member));
    }

    public function testItDoesntFireJobWhenUpdateNotNeeded(): void
    {
        Queue::fake();
        $member = Member::factory()->defaults()->create();

        Member::enableGeolocation();
        $member->update(['nickname' => 'test']);

        Queue::assertNotPushed(FillCoordsJob::class);
    }

    public function testItFiresJobWhenAddressUpdateNeeded(): void
    {
        Queue::fake();
        $member = Member::factory()->defaults()->create();

        Member::enableGeolocation();
        $member->update(['address' => 'abcdef']);

        Queue::assertPushed(FillCoordsJob::class, fn ($job) => $job->model->address === 'abcdef');
    }

    public function testItFiresJobWhenZipUpdateNeeded(): void
    {
        Queue::fake();
        $member = Member::factory()->defaults()->create();

        Member::enableGeolocation();
        $member->update(['zip' => '33445']);

        Queue::assertPushed(FillCoordsJob::class, fn ($job) => $job->model->zip === '33445');
    }
}
