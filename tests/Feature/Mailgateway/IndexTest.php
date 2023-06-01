<?php

namespace Tests\Feature\Mailgateway;

use App\Mailgateway\Models\Mailgateway;
use App\Mailgateway\Types\LocalType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->login()->loginNami();
    }

    public function testItCanViewIndexPage(): void
    {
        $response = $this->get('/setting/mailgateway');

        $response->assertOk();
    }

    public function testItDisplaysGateways(): void
    {
        $this->withoutExceptionHandling();
        Mailgateway::factory()->type(LocalType::class, [])->name('Lore')->domain('example.com')->create();

        $response = $this->get('/setting/mailgateway');

        $this->assertInertiaHas('example.com', $response, 'data.data.0.domain');
        $this->assertInertiaHas('Lore', $response, 'data.data.0.name');
        $this->assertInertiaHas('Lokal', $response, 'data.data.0.type_human');
        $this->assertInertiaHas(true, $response, 'data.data.0.works');
    }

    public function testItHasMeta(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/setting/mailgateway');

        $this->assertInertiaHas(route('api.mailgateway.store'), $response, 'data.meta.links.store');
    }
}
