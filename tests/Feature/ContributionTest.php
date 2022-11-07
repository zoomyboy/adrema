<?php

namespace Tests\Feature;

use App\Country;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\Tex\Tex;

class ContributionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @testWith ["App\\Contribution\\SolingenDocument", ["Super tolles Lager", "Max Muster", "Jane Muster", "15.06.1991"]]
     *  ["App\\Contribution\\DvDocument", ["Muster, Max", "Muster, Jane", "15.06.1991", "42777 SG"]]
     *
     * @param array<int, string> $bodyChecks
     */
    public function testItCompilesContributionDocuments(string $type, array $bodyChecks): void
    {
        $this->withoutExceptionHandling();
        Tex::spy();
        $this->login()->loginNami();
        $country = Country::factory()->create();
        $member1 = Member::factory()->defaults()->create(['firstname' => 'Max', 'lastname' => 'Muster']);
        $member2 = Member::factory()->defaults()->create(['firstname' => 'Jane', 'lastname' => 'Muster']);

        $response = $this->call('GET', '/contribution/generate', [
            'country' => $country->id,
            'dateFrom' => '1991-06-15',
            'dateUntil' => '1991-06-16',
            'eventName' => 'Super tolles Lager',
            'members' => [$member1->id, $member2->id],
            'type' => $type,
            'zipLocation' => '42777 SG',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertOk();
        Tex::assertCompiled($type, fn ($document) => $document->hasAllContent($bodyChecks));
    }
}
