<?php

namespace Tests\Feature\Member;

use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NominatimTest extends TestCase
{
    use DatabaseTransactions;

    public function testItGetsCoordinates(): void
    {
        Member::enableGeolocation();
        Http::fake([
            'https://nominatim.openstreetmap.org/search/*' => Http::response('[{"place_id":262556558,"osm_type":"way","osm_id":785100564,"lat":"51.1775766","lon":"7.025311390606571"}]', 200),
        ]);
        $this->login()->loginNami();

        $member = Member::factory()->defaults()->create(['address' => 'Itterstr 11', 'zip' => '55667', 'location' => 'Koln']);

        $this->assertEquals(51.1775766, $member->fresh()->lat);
        $this->assertEquals(7.0253113906066, $member->fresh()->lon);
        Http::assertSent(fn ($request) => 'https://nominatim.openstreetmap.org/search/Itterstr%2011%2C%2055667%20Koln?format=json&addressdetails=1' === $request->url());
    }
}
