<?php

namespace Tests\Feature\Initializer;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Authentication\Auth;
use Zoomyboy\LaravelNami\Fakes\SearchLayerFake;

class GetSearchLayerActionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
        Auth::success(333, 'secret');
    }

    public function testItFindsRoots(): void
    {
        $this->withoutExceptionHandling();
        app(SearchLayerFake::class)->fetches('1', [
            ['descriptor' => 'aa', 'id' => 5],
        ]);

        $response = $this->postJson('/nami/get-search-layer', [
            'layer' => 0,
            'parent' => null,
            'mglnr' => 333,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('0.name', 'aa');
        $response->assertJsonPath('0.id', 5);
    }

    public function testItFindsFirstLayer(): void
    {
        $this->withoutExceptionHandling();
        app(SearchLayerFake::class)->fetches('2/gruppierung1/20', [
            ['descriptor' => 'aa', 'id' => 5],
        ]);

        $response = $this->postJson('/nami/get-search-layer', [
            'layer' => 1,
            'parent' => 20,
            'mglnr' => 333,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('0.name', 'aa');
        $response->assertJsonPath('0.id', 5);
    }

    public function testItFindsSecondLayer(): void
    {
        $this->withoutExceptionHandling();
        app(SearchLayerFake::class)->fetches('3/gruppierung2/30', [
            ['descriptor' => 'aa', 'id' => 5],
        ]);

        $response = $this->postJson('/nami/get-search-layer', [
            'layer' => 2,
            'parent' => 30,
            'mglnr' => 333,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('0.name', 'aa');
        $response->assertJsonPath('0.id', 5);
    }
}
