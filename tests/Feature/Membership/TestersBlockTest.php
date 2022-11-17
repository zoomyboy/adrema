<?php

namespace Tests\Feature\Membership;

use App\Member\Member;
use App\Member\Membership;
use App\Membership\TestersBlock;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TestersBlockTest extends TestCase
{
    use DatabaseTransactions;

    public function testItHasData(): void
    {
        $this->login()->loginNami();

        Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('Schnuppermitgliedschaft', 7, 'Wölfling', 8)->state(['created_at' => now()->subMonths(10)]))
            ->create(['firstname' => 'Max', 'lastname' => 'Muster']);

        $data = app(TestersBlock::class)->render();

        $this->assertEquals([
            'members' => [
                [
                    'name' => 'Max Muster',
                    'try_ends_at' => now()->subMonths(10)->addWeeks(8)->format('d.m.Y'),
                    'try_ends_at_human' => now()->subMonths(10)->addWeeks(8)->diffForHumans(),
                ],
            ],
        ], $data);
    }
}
