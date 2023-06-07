<?php

namespace Tests\Feature\Mailgateway;

use App\Mailgateway\Types\LocalType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\MailgatewayRequestFactory;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->login()->loginNami();
    }

    public function testItCanStoreALocalGateway(): void
    {
        $response = $this->post('/api/mailgateway', MailgatewayRequestFactory::new()->name('lala')->type(LocalType::class, [])->domain('example.com')->create());

        $response->assertOk();

        $this->assertDatabaseHas('mailgateways', [
            'domain' => 'example.com',
            'name' => 'lala',
            'type' => json_encode([
                'cls' => LocalType::class,
                'params' => [],
            ]),
        ]);
    }
}
