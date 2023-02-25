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
        Country::factory()->create(['name' => 'Deutschland']);
        Member::factory()->defaults()->create(['firstname' => 'Max', 'lastname' => 'Muster']);
        Member::factory()->defaults()->create(['firstname' => 'Jane', 'lastname' => 'Muster']);

        $response = $this->get('/contribution');

        $this->assertInertiaHas('Jane', $response, 'allMembers.data.0.firstname');
        $this->assertInertiaHas([
            'class' => DvDocument::class,
            'title' => 'Für DV erstellen',
        ], $response, 'compilers.0');
    }
}
