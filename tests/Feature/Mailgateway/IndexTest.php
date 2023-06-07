<?php

namespace Tests\Feature\Mailgateway;

use App\Mailgateway\Models\Mailgateway;
use App\Mailgateway\Types\LocalType;
use App\Mailgateway\Types\MailmanType;
use App\Mailman\Support\MailmanService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phake;
use Tests\RequestFactories\MailmanTypeRequest;
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

    public function testItDisplaysLocalGateways(): void
    {
        $this->withoutExceptionHandling();
        $mailgateway = Mailgateway::factory()->type(LocalType::class, [])->name('Lore')->domain('example.com')->create();

        $response = $this->get('/setting/mailgateway');

        $this->assertInertiaHas('example.com', $response, 'data.data.0.domain');
        $this->assertInertiaHas('Lore', $response, 'data.data.0.name');
        $this->assertInertiaHas('Lokal', $response, 'data.data.0.type_human');
        $this->assertInertiaHas(true, $response, 'data.data.0.works');
        $this->assertInertiaHas($mailgateway->id, $response, 'data.data.0.id');
        $this->assertInertiaHas(route('mailgateway.update', ['mailgateway' => $mailgateway->id]), $response, 'data.data.0.links.update');
    }

    public function testItDisplaysMailmanGateways(): void
    {
        $this->stubIo(MailmanService::class, function ($mock) {
            Phake::when($mock)->setCredentials()->thenReturn($mock);
            Phake::when($mock)->check()->thenReturn(true);
        });
        $this->withoutExceptionHandling();
        Mailgateway::factory()->type(MailmanType::class, MailmanTypeRequest::new()->create())->create();

        $this->get('/setting/mailgateway')->assertOk();
    }

    public function testItHasMeta(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/setting/mailgateway');

        $this->assertInertiaHas(route('mailgateway.store'), $response, 'data.meta.links.store');
        $this->assertInertiaHas([
            'id' => null,
            'name' => '-- kein --',
        ], $response, 'data.meta.types.0');
        $this->assertInertiaHas([
            'id' => LocalType::class,
            'name' => 'Lokal',
        ], $response, 'data.meta.types.1');
        $this->assertInertiaHas([
            'id' => MailmanType::class,
            'fields' => [
                [
                    'name' => 'url',
                    'is_required' => true,
                ],
            ],
        ], $response, 'data.meta.types.2');
        $this->assertInertiaHas([
            'domain' => '',
            'name' => '',
            'type' => [
                'params' => [],
                'cls' => null,
            ],
        ], $response, 'data.meta.default');
    }
}
