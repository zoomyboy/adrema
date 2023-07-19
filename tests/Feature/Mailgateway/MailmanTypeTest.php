<?php

namespace Tests\Feature\Mailgateway;

use App\Mailgateway\Types\MailmanType;
use App\Mailman\Support\MailmanService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phake;
use Tests\TestCase;

class MailmanTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function testItChecksForWorks(): void
    {
        $this->withoutExceptionHandling();
        $this->stubIo(MailmanService::class, function ($mock) {
            Phake::when($mock)->setCredentials('https://example.com', 'user', 'secret')->thenReturn($mock);
            Phake::when($mock)->check()->thenReturn(true);
            Phake::when($mock)->setOwner('owner@example.com')->thenReturn($mock);
        });
        $type = app(MailmanType::class)->setParams([
            'url' => 'https://example.com',
            'user' => 'user',
            'password' => 'secret',
            'owner' => 'owner@example.com',
        ]);

        $this->assertTrue($type->works());
    }

    public function testItCanReturnFalse(): void
    {
        $this->withoutExceptionHandling();
        $this->stubIo(MailmanService::class, function ($mock) {
            Phake::when($mock)->setCredentials('https://example.com', 'user', 'secret')->thenReturn($mock);
            Phake::when($mock)->check()->thenReturn(false);
        });
        $type = app(MailmanType::class)->setParams([
            'url' => 'https://example.com',
            'user' => 'user',
            'password' => 'secret',
        ]);

        $this->assertFalse($type->works());
    }
}
