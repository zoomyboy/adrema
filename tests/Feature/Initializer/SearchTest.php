<?php

namespace Tests\Feature\Initializer;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Authentication\Auth;
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
            ['id' => 2, 'entries_gruppierungId' => 100, 'entries_vorname' => 'Max', 'entries_nachname' => 'Muster', 'entries_stufe' => 'Wölfling', 'entries_geburtsDatum' => '2013-07-04 00:00:00'],
            ['id' => 2, 'entries_gruppierungId' => 150, 'entries_vorname' => 'Jane', 'entries_nachname' => 'Muster', 'entries_stufe' => 'Wölfling', 'entries_geburtsDatum' => '2013-07-04 00:00:00'],
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
        $repsonse->assertJsonPath('data.0.agegroup', 'Wölfling');
        app(SearchFake::class)->assertFetched(1, 0, 10, [
            'gruppierung1Id' => 100,
            'gruppierung2Id' => 101,
        ]);
    }

    public function testItDoesntNeedFirstname(): void
    {
        $this->withoutExceptionHandling();
        app(SearchFake::class)->fetches(1, 0, 10, [
            ['id' => 2, 'entries_gruppierungId' => 100, 'entries_vorname' => null, 'entries_nachname' => 'Muster', 'entries_stufe' => null, 'entries_geburtsDatum' => 'lalala'],
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
            ['id' => 2, 'entries_gruppierungId' => 100, 'entries_vorname' => 'Max', 'entries_nachname' => 'Muster'],
            ['id' => 2, 'entries_gruppierungId' => 100, 'entries_vorname' => 'Max', 'entries_nachname' => 'Muster'],
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
