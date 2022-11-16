<?php

namespace Tests\Feature;

use App\Member\Member;
use App\Member\Membership;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HomeTest extends TestCase
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
        Member::factory()->has(Membership::factory()->in('€ LeiterIn', 2, 'Wölfling', 3))
            ->defaults()
            ->create();

        $this->login()->loginNami();

        $response = $this->get('/');

        $this->assertInertiaHas([
            'slug' => 'biber',
            'name' => 'Biber',
            'count' => 3,
        ], $response, 'data.groups.0');
        $this->assertInertiaHas([
            'slug' => 'woelfling',
            'name' => 'Wölfling',
            'count' => 4,
        ], $response, 'data.groups.1');
    }
}
