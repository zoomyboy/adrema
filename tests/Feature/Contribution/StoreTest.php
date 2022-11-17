<?php

namespace Tests\Feature\Contribution;

use App\Country;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\Tex\Tex;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @testWith ["App\\Contribution\\Documents\\SolingenDocument", ["Super tolles Lager", "Max Muster", "Jane Muster", "15.06.1991"]]
     *  ["App\\Contribution\\Documents\\DvDocument", ["Muster, Max", "Muster, Jane", "15.06.1991", "42777 SG"]]
     *  ["App\\Contribution\\Documents\\RemscheidDocument", ["Max", "Muster", "Jane", "15.06.1991", "Maxstr 44, 42719 SG"]]
     *
     * @param array<int, string> $bodyChecks
     */
    public function testItCompilesContributionDocuments(string $type, array $bodyChecks): void
    {
        $this->withoutExceptionHandling();
        Tex::spy();
        $this->login()->loginNami();
        $country = Country::factory()->create();
        $member1 = Member::factory()->defaults()->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Max', 'lastname' => 'Muster']);
        $member2 = Member::factory()->defaults()->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Jane', 'lastname' => 'Muster']);

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
