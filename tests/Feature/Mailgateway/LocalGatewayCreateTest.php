<?php

namespace Tests\Feature\Mailgateway;

use App\Mailgateway\Types\LocalType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LocalGatewayCreateTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCanCreateALocalGateway(): void
    {
        $this->login()->loginNami();

        $this->withoutExceptionHandling();
        $response = $this->post('/api/mailgateway', [
            'type' => [
                'cls' => LocalType::class,
                'params' => [],
            ],
            'name' => 'lala',
            'domain' => 'example.com',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('mailgateways', [
            'name' => 'lala',
            'domain' => 'example.com',
            'type' => json_encode([
                'cls' => LocalType::class,
                'params' => [],
            ]),
        ]);
    }
}
