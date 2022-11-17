<?php

namespace Tests\Feature\Membership;

use App\Efz\EfzPendingBlock;
use App\Member\Member;
use App\Member\Membership;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EfzPendingBlockTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDisplaysEfzPending(): void
    {
        $this->withoutExceptionHandling();
        Member::factory()
            ->has(Membership::factory()->in('€ LeiterIn', 1, 'Biber', 2))
            ->defaults()
            ->create(['firstname' => 'Max', 'lastname' => 'Muster', 'efz' => now()->subYear()]);
        Member::factory()
            ->has(Membership::factory()->in('€ Mitglied', 1, 'Biber', 2))
            ->defaults()
            ->create(['firstname' => 'Jane', 'lastname' => 'Muster', 'efz' => now()->subYear()]);
        Member::factory()
            ->has(Membership::factory()->in('€ LeiterIn', 1, 'Biber', 2))
            ->defaults()
            ->create(['firstname' => 'Mae', 'lastname' => 'Muster', 'efz' => now()->subYears(5)->startOfYear()]);
        Member::factory()
            ->has(Membership::factory()->in('€ LeiterIn', 1, 'Biber', 2))
            ->defaults()
            ->create(['firstname' => 'Joe', 'lastname' => 'Muster', 'efz' => now()->subYears(5)->endOfYear()]);
        Member::factory()
            ->has(Membership::factory()->in('€ LeiterIn', 1, 'Biber', 2))
            ->defaults()
            ->create(['firstname' => 'Moa', 'lastname' => 'Muster', 'efz' => null]);
        Member::factory()
            ->has(Membership::factory()->in('€ Mitglied', 1, 'Biber', 2))
            ->defaults()
            ->create(['firstname' => 'Doe', 'lastname' => 'Muster', 'efz' => now()->subYears(5)]);

        $data = app(EfzPendingBlock::class)->render()['data'];

        $this->assertEquals([
            'members' => ['Joe Muster', 'Mae Muster', 'Moa Muster'],
        ], $data);
    }
}
