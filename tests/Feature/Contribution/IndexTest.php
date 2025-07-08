<?php

namespace Tests\Feature\Contribution;

use App\Contribution\Documents\RdpNrwDocument;
use App\Country;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    public function testItHasContributionIndex(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $country = Country::factory()->create(['name' => 'Deutschland']);
        Member::factory()->defaults()->create(['firstname' => 'Max', 'lastname' => 'Muster']);
        Member::factory()->defaults()->create(['firstname' => 'Jane', 'lastname' => 'Muster']);

        $response = $this->get('/contribution');

        $this->assertInertiaHas([
            'id' => RdpNrwDocument::class,
            'name' => 'RdP NRW',
        ], $response, 'compilers.0');
        $this->assertInertiaHas([
            'id' => $country->id,
            'name' => $country->name,
        ], $response, 'countries.0');
        $this->assertInertiaHas([
            'country' => $country->id,
        ], $response, 'data');
    }
}
