<?php

namespace Tests\EndToEnd;

use App\Group;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use App\Member\Member;
use App\Payment\Payment;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\RequestFactories\Child;
use Tests\TestCase;

class MemberIndexTest extends TestCase
{
    use DatabaseMigrations;

    public function testItHandlesFullTextSearch(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create();
        Member::factory()->defaults()->for($group)->create(['firstname' => '::firstname::']);
        Member::factory()->defaults()->for($group)->create(['firstname' => '::gggname::']);

        $response = $this->callFilter('member.index', ['search' => '::firstname::']);

        $this->assertCount(1, $this->inertia($response, 'data.data'));
    }

    public function testItHandlesAddress(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create();
        Member::factory()->defaults()->for(Group::factory())->create(['address' => '']);
        Member::factory()->defaults()->for(Group::factory())->create(['zip' => '']);
        Member::factory()->defaults()->for(Group::factory())->create(['location' => '']);

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
