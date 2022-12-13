<?php

namespace Tests\Feature\Membership;

use App\Member\Member;
use App\Member\Membership;
use App\Membership\AgeGroupCountBlock;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AgeGroupCountBlockTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDisplaysAgeGroups(): void
    {
        $this->withoutExceptionHandling();
        Member::factory()->count(3)
            ->has(Membership::factory()->in('€ Mitglied', 1, 'Biber', 2))
            ->defaults()
            ->create();
        Member::factory()->count(4)
            ->has(Membership::factory()->in('€ Mitglied', 1, 'Wölfling', 3))
            ->defaults()
            ->create();
        Member::factory()->count(2)->has(Membership::factory()->in('€ LeiterIn', 2, 'Wölfling', 3))
            ->defaults()
            ->create();
        Member::factory()->count(2)->has(Membership::factory()->in('€ LeiterIn', 2, 'Jungpfadfinder', 3))
            ->defaults()
            ->create();

        $data = app(AgeGroupCountBlock::class)->render()['data'];

        $this->assertEquals([
            'groups' => [
                ['slug' => 'biber', 'name' => 'Biber', 'count' => 3],
                ['slug' => 'woelfling', 'name' => 'Wölfling', 'count' => 4],
                ['slug' => 'leiter', 'name' => 'Leiter', 'count' => 4],
            ],
        ], $data);
    }
}
