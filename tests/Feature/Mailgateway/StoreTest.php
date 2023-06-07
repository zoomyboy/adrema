<?php

namespace Tests\Feature\Mailgateway;

use App\Mailgateway\Types\LocalType;
use App\Mailgateway\Types\MailmanType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phake;
use Tests\RequestFactories\MailgatewayRequestFactory;
use Tests\RequestFactories\MailmanTypeRequest;
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
        $response = $this->postJson('/api/mailgateway', MailgatewayRequestFactory::new()->name('lala')->type(LocalType::class, [])->domain('example.com')->create());

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

    public function testItCanStoreAMailmanGateway(): void
    {
        $typeParams = ['url' => 'https://example.com', 'user' => 'user', 'password' => 'secret'];
        $this->stubIo(MailmanType::class, function ($mock) use ($typeParams) {
            Phake::when($mock)->setParams($typeParams)->thenReturn($mock);
            Phake::when($mock)->works()->thenReturn(true);
        });
        $this->postJson('/api/mailgateway', MailgatewayRequestFactory::new()->type(MailmanType::class, MailmanTypeRequest::new()->create($typeParams))->create());

        $this->assertDatabaseHas('mailgateways', [
            'type' => json_encode([
                'cls' => MailmanType::class,
                'params' => $typeParams,
            ]),
        ]);
    }

    public function testItThrowsErrorWhenMailmanConnectionFailed(): void
    {
        $typeParams = ['url' => 'https://example.com', 'user' => 'user', 'password' => 'secret'];
        $this->stubIo(MailmanType::class, function ($mock) use ($typeParams) {
            Phake::when($mock)->setParams($typeParams)->thenReturn($mock);
            Phake::when($mock)->works()->thenReturn(false);
        });
        $this->postJson('/api/mailgateway', MailgatewayRequestFactory::new()->type(MailmanType::class, MailmanTypeRequest::new()->create($typeParams))->create())
             ->assertJsonValidationErrors('connection');
    }

    public function testItValidatesCustomFields(): void
    {
        $typeParams = ['url' => 'https://example.com', 'user' => '', 'password' => 'secret'];
        $this->stubIo(MailmanType::class, function ($mock) use ($typeParams) {
            Phake::when($mock)->setParams($typeParams)->thenReturn($mock);
            Phake::when($mock)->works()->thenReturn(false);
        });
        $this->postJson('/api/mailgateway', MailgatewayRequestFactory::new()->type(MailmanType::class, MailmanTypeRequest::new()->create($typeParams))->create())
             ->assertJsonValidationErrors(['type.params.user' => 'Benutzer ist erforderlich.']);
    }

    public function testItValidatesType(): void
    {
        $this->postJson('/api/mailgateway', MailgatewayRequestFactory::new()->missingType()->create())
            ->assertJsonValidationErrors('type.cls');
    }

    public function testItValidatesNameAndDomain(): void
    {
        $this->postJson('/api/mailgateway', MailgatewayRequestFactory::new()->withoutName()->withoutDomain()->create())
            ->assertJsonValidationErrors('domain')
            ->assertJsonValidationErrors('name');
    }
}
