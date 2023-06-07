<?php

namespace Tests\Feature\Mailgateway;

use App\Mailgateway\Models\Mailgateway;
use App\Mailgateway\Types\LocalType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\MailgatewayRequestFactory;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->login()->loginNami();
    }

    public function testItCanUpdateALocalGateway(): void
    {
        $mailgateway = Mailgateway::factory()->type(LocalType::class, [])->create();
        $response = $this->patchJson("/api/mailgateway/{$mailgateway->id}", MailgatewayRequestFactory::new()->name('lala')->type(LocalType::class, [])->domain('example.com')->create());

        $response->assertOk();

        $this->assertDatabaseCount('mailgateways', 1);
    }
}
