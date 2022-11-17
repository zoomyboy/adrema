<?php

namespace Tests\Feature\Contribution;

use App\Contribution\Documents\DvDocument;
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
        $member1 = Member::factory()->defaults()->create(['firstname' => 'Max', 'lastname' => 'Muster']);
        $member2 = Member::factory()->defaults()->create(['firstname' => 'Jane', 'lastname' => 'Muster']);

        $response = $this->get('/contribution');

        $this->assertInertiaHas('Jane', $response, 'allMembers.0.firstname');
        $this->assertInertiaHas([
            'class' => DvDocument::class,
            'title' => 'Für DV erstellen',
        ], $response, 'compilers.0');
    }
}
