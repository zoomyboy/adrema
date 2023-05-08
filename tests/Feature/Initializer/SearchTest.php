<?php

namespace Tests\Feature\Initializer;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Authentication\Auth;
use Zoomyboy\LaravelNami\Data\MemberEntry;
use Zoomyboy\LaravelNami\Fakes\SearchFake;

class SearchTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testItSearchesForMembers(): void
    {
        $this->withoutExceptionHandling();
        app(SearchFake::class)->fetches(1, 0, 10, [
            MemberEntry::factory()->state(['id' => 2, 'groupId' => 100, 'firstname' => 'Max', 'lastname' => 'Muster', 'birthday' => '2013-07-04 00:00:00'])->toMember(),
            MemberEntry::factory()->state(['id' => 2, 'groupId' => 150, 'firstname' => 'Jane', 'lastname' => 'Muster', 'birthday' => '2013-07-04 00:00:00'])->toMember(),
        ]);
        Auth::success(333, 'secret');

        $repsonse = $this->postJson('/nami/search', [
            'params' => [
                'gruppierung1Id' => 100,
                'gruppierung2Id' => 101,
            ],
            'mglnr' => 333,
            'password' => 'secret',
        ]);

        $repsonse->assertOk();
        $repsonse->assertJsonPath('data.0.birthday_human', '04.07.2013');
        $repsonse->assertJsonPath('data.0.firstname', 'Max');
        $repsonse->assertJsonPath('data.0.lastname', 'Muster');
        $repsonse->assertJsonPath('data.0.id', 2);
        $repsonse->assertJsonPath('data.0.groupId', 100);
        app(SearchFake::class)->assertFetched(1, 0, 10, [
            'gruppierung1Id' => 100,
            'gruppierung2Id' => 101,
        ]);
    }

    public function testItDoesntNeedFirstname(): void
    {
        $this->withoutExceptionHandling();
        app(SearchFake::class)->fetches(1, 0, 10, [
            MemberEntry::factory()->noFirstname()->toMember(),
        ]);
        Auth::success(333, 'secret');

        $this->postJson('/nami/search', [
            'params' => [],
            'mglnr' => 333,
            'password' => 'secret',
            'birthday_human' => null,
            'agegroup' => null,
        ])->assertJsonPath('data.0.firstname', null);
    }

    public function testItGetsPageInformation(): void
    {
        $this->withoutExceptionHandling();
        app(SearchFake::class)->fetches(2, 10, 10, [
            MemberEntry::factory()->toMember(),
            MemberEntry::factory()->toMember(),
        ]);
        Auth::success(333, 'secret');

        $response = $this->postJson('/nami/search', [
            'params' => [
                'gruppierung1Id' => 100,
                'gruppierung2Id' => 101,
            ],
            'page' => 2,
            'mglnr' => 333,
            'password' => 'secret',
        ]);

        $response->assertOk();
        app(SearchFake::class)->assertFetched(2, 10, 10, [
            'gruppierung1Id' => 100,
            'gruppierung2Id' => 101,
        ]);
    }
}
