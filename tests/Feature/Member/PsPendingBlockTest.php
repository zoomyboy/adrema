<?php

namespace Tests\Feature\Member;

use App\Member\Member;
use App\Member\Membership;
use App\Member\PsPendingBlock;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PsPendingBlockTest extends TestCase
{
    use DatabaseTransactions;

    public function testItRendersContent(): void
    {
        $noPsAtAll = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Wölfling', 8))
            ->create(['firstname' => 'Jane', 'lastname' => 'Doe']);
        $validPs = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Wölfling', 8))
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Wölfling', 8))
            ->create(['firstname' => 'Max', 'lastname' => 'Doe', 'ps_at' => now()->subYears(4)]);
        $validMorePs = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Wölfling', 8))
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Wölfling', 8))
            ->create(['firstname' => 'Joe', 'lastname' => 'Doe', 'more_ps_at' => now()->subYears(4)]);
        $invalidPs = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Wölfling', 8))
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Wölfling', 8))
            ->create(['firstname' => 'Mike', 'lastname' => 'Doe', 'ps_at' => now()->subYears(5)]);
        $invalidMorePs = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Wölfling', 8))
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Wölfling', 8))
            ->create(['firstname' => 'Nora', 'lastname' => 'Doe', 'more_ps_at' => now()->subYears(5)]);
        $invalidPsButValidMorePs = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Wölfling', 8))
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Wölfling', 8))
            ->create(['firstname' => 'Hey', 'lastname' => 'Doe', 'ps_at' => now()->subYears(10), 'more_ps_at' => now()->subYears(3)]);
        $notALeader = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ Mitglied', 5, 'Wölfling', 8))
            ->create(['firstname' => 'Mae', 'lastname' => 'Doe']);

        $data = app(PsPendingBlock::class)->render()['data'];

        $this->assertEquals([
            'members' => [
                ['fullname' => 'Jane Doe'],
                ['fullname' => 'Mike Doe'],
                ['fullname' => 'Nora Doe'],
            ],
        ], $data);
    }
}
