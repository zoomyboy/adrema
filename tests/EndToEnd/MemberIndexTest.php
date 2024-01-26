<?php

namespace Tests\EndToEnd;

use App\Group;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Laravel\Scout\Console\SyncIndexSettingsCommand;
use Tests\TestCase;

class MemberIndexTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('scout.driver', 'meilisearch');
        Artisan::call(SyncIndexSettingsCommand::class);
    }

    public function testItHandlesFullTextSearch(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create();
        Member::factory()->defaults()->for($group)->create(['firstname' => 'Alexander']);
        Member::factory()->defaults()->for($group)->create(['firstname' => 'Heinrich']);

        $response = $this->callFilter('member.index', ['search' => 'Alexander']);

        $this->assertCount(1, $this->inertia($response, 'data.data'));
    }

    public function testItHandlesAddress(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create();
        Member::factory()->defaults()->for($group)->create(['address' => '']);
        Member::factory()->defaults()->for($group)->create(['zip' => '']);
        Member::factory()->defaults()->for($group)->create(['location' => '']);

        $response = $this->callFilter('member.index', ['has_full_address' => true]);
        $noResponse = $this->callFilter('member.index', ['has_full_address' => false]);

        $this->assertCount(0, $this->inertia($response, 'data.data'));
        $this->assertCount(3, $this->inertia($noResponse, 'data.data'));
    }

    public function testItHandlesBirthday(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create();
        $member = Member::factory()->defaults()->for(Group::factory())->create(['birthday' => null]);

        $response = $this->callFilter('member.index', ['has_birthday' => true]);

        $this->assertCount(0, $this->inertia($response, 'data.data'));
        $member->delete();
    }

    public function testItFiltersForSearchButNotForPayments(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create();
        Member::factory()->defaults()->for($group)
            ->has(InvoicePosition::factory()->for(Invoice::factory()))
            ->create(['firstname' => '::firstname::']);
        Member::factory()->defaults()->for($group)->create(['firstname' => '::firstname::']);

        $response = $this->callFilter('member.index', ['search' => '::firstname::', 'ausstand' => true]);

        $this->assertCount(1, $this->inertia($response, 'data.data'));
    }
}
