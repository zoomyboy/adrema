<?php

namespace Tests\Unit\Mailman;

use App\Mailman\Support\MailmanService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    public function testItChecksForCredentials(): void
    {
        Http::fake([
            'http://mailman.test/api/system/versions' => Http::response('', 200),
        ]);

        $result = app(MailmanService::class)->setCredentials('http://mailman.test/api/', 'user', 'secret')->check();

        $this->assertTrue($result);

        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => 'GET' === $request->method() && 'http://mailman.test/api/system/versions' === $request->url() && $request->header('Authorization') === ['Basic '.base64_encode('user:secret')]);
    }

    public function testItFailsWhenChckingCredentials(): void
    {
        Http::fake([
            'http://mailman.test/api/system/versions' => Http::response('', 401),
        ]);

        $result = app(MailmanService::class)->setCredentials('http://mailman.test/api/', 'user', 'secret')->check();

        $this->assertFalse($result);

        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => 'GET' === $request->method() && 'http://mailman.test/api/system/versions' === $request->url() && $request->header('Authorization') === ['Basic '.base64_encode('user:secret')]);
    }
}
