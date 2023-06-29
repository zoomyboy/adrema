<?php

namespace Tests\EndToEnd;

use App\Group;
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

    public function testItFiltersForSearchButNotForPayments(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create();
        Member::factory()->defaults()->for($group)
            ->has(Payment::factory()->notPaid()->subscription('tollerbeitrag', [
                new Child('a', 5400),
            ]))
            ->create(['firstname' => '::firstname::']);
        Member::factory()->defaults()->for($group)->create(['firstname' => '::firstname::']);

        $response = $this->callFilter('member.index', ['search' => '::firstname::', 'ausstand' => true]);

        $this->assertCount(1, $this->inertia($response, 'data.data'));
    }
}
