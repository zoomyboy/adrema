<?php

namespace Tests\Unit\Mailman;

use App\Mailman\Data\MailingList;
use App\Mailman\Exceptions\MailmanServiceException;
use App\Mailman\Support\MailmanService;
use Generator;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\RequestFactories\MailmanListRequestFactory;
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
        Http::assertSent(fn ($request) => 'GET' === $request->method() && 'http://mailman.test/api/system/versions' === $request->url() && $request->header('Authorization') === ['Basic ' . base64_encode('user:secret')]);
    }

    public function testItFailsWhenChckingCredentials(): void
    {
        Http::fake([
            'http://mailman.test/api/system/versions' => Http::response('', 401),
        ]);

        $result = app(MailmanService::class)->setCredentials('http://mailman.test/api/', 'user', 'secret')->check();

        $this->assertFalse($result);

        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => 'GET' === $request->method() && 'http://mailman.test/api/system/versions' === $request->url() && $request->header('Authorization') === ['Basic ' . base64_encode('user:secret')]);
    }

    public function testItGetsMembersFromList(): void
    {
        Http::fake([
            'http://mailman.test/api/lists/listid/roster/member?page=1&count=10' => Http::response(json_encode([
                'entries' => [
                    ['email' => 'test@example.com', 'self_link' => 'https://example.com/994'],
                ],
                'total_size' => 2,
            ]), 200),
        ]);

        $result = app(MailmanService::class)->setCredentials('http://mailman.test/api/', 'user', 'secret')->members(MailingList::toFactory()->id('listid')->toData())->first();

        $this->assertEquals(994, $result->memberId);
        $this->assertEquals('test@example.com', $result->email);
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => 'GET' === $request->method() && 'http://mailman.test/api/lists/listid/roster/member?page=1&count=10' === $request->url() && $request->header('Authorization') === ['Basic ' . base64_encode('user:secret')]);
    }

    public function testItThrowsExceptionWhenLoginFailed(): void
    {
        $this->expectException(MailmanServiceException::class);
        Http::fake([
            'http://mailman.test/api/lists/listid/roster/member?page=1&count=10' => Http::response('', 401),
        ]);

        app(MailmanService::class)->setCredentials('http://mailman.test/api/', 'user', 'secret')->members(MailingList::toFactory()->id('listid')->toData())->first();
    }

    public function testItCanGetLists(): void
    {
        Http::fake([
            'http://mailman.test/api/lists?page=1&count=10' => Http::sequence()
                ->push(json_encode([
                    'entries' => [
                        MailmanListRequestFactory::new()->create(['display_name' => 'Eltern', 'fqdn_listname' => 'eltern@example.com']),
                        MailmanListRequestFactory::new()->create(['display_name' => 'Eltern2', 'fqdn_listname' => 'eltern2@example.com']),
                    ],
                    'start' => 0,
                    'total_size' => 2,
                ]), 200),
        ]);

        $lists = app(MailmanService::class)->setCredentials('http://mailman.test/api/', 'user', 'secret')->getLists()->all();
        $this->assertCount(2, $lists);
        $this->assertInstanceOf(MailingList::class, $lists[0]);
        $this->assertEquals('Eltern', $lists[0]->displayName);
    }

    public static function listDataProvider(): Generator
    {
        foreach (range(3, 40) as $i) {
            yield [
                collect(range(1, $i))
                    ->map(fn ($num) => ['email' => 'test' . $num . '@example.com', 'self_link' => 'https://example.com/994'])
                    ->toArray(),
            ];
        }
    }

    #[DataProvider('listDataProvider')]
    public function testItReturnsMoreThanOneResult(array $totals): void
    {
        $totals = collect($totals);
        foreach ($totals->chunk(10) as $n => $chunk) {
            Http::fake([
                'http://mailman.test/api/lists/listid/roster/member?page=' . ($n + 1) . '&count=10' => Http::response(json_encode([
                    'entries' => $chunk,
                    'total_size' => $totals->count(),
                ]), 200),
            ]);
        }

        $result = app(MailmanService::class)->setCredentials('http://mailman.test/api/', 'user', 'secret')->members(MailingList::toFactory()->id('listid')->toData());

        $this->assertCount($totals->count(), $result->toArray());
        Http::assertSentCount($totals->chunk(10)->count());
    }

    public function testItCanCreateLists(): void
    {
        Http::fakeSequence()
            ->push('', 201)
            ->push(json_encode([
                'entries' => [
                    MailmanListRequestFactory::new()->create(['list_id' => 'test.example.com', 'fqdn_listname' => 'test@example.com']),
                ],
                'start' => 0,
                'total_size' => 0,
            ]), 200)
            ->push('', 204)
            ->push('', 201);
        $service = app(MailmanService::class)->setCredentials('http://mailman.test/api/', 'user', 'secret')->setOwner('test@zoomyboy.de');
        $list = $service->createList('test@example.com');

        $this->assertInstanceOf(MailingList::class, $list);

        Http::assertSent(fn ($request) => 'http://mailman.test/api/lists' === $request->url() && 'POST' === $request->method());
    }
}
