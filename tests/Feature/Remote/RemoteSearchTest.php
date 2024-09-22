<?php

namespace Tests\Feature\Nami;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Testing\TestResponse;
use Tests\EndToEndTestCase;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Authentication\Auth;
use Zoomyboy\LaravelNami\Data\MemberEntry;
use Zoomyboy\LaravelNami\Fakes\SearchFake;

class RemoteSearchTest extends TestCase
{

    use DatabaseTransactions;

    public function testItCanLoginRemotelyWithUser(): void
    {
        Auth::success(90100, 'secret');
        $response = $this->loginRemotely(90100, 'secret');
        $response->assertOk();

        $accessTokenPayload = Crypt::decryptString($response->json('access_token'));
        $this->assertEquals(['mglnr' => 90100, 'password' => 'secret'], json_decode($accessTokenPayload, true));
    }

    public function testItCanSearchForOwnGroupMembers(): void
    {
        Auth::success(90100, 'secret');
        app(SearchFake::class)->fetches(1, 0, 50, [
            MemberEntry::toFactory()->toMember(['groupId' => 100, 'id' => 20, 'memberId' => 56, 'firstname' => 'Max', 'lastname' => 'Muster']),
        ]);
        $accessToken = $this->loginRemotely()->json('access_token');

        $this->postJson(route('remote.nami.search'), [], ['Authorization' => 'Bearer ' . $accessToken])
            ->assertOk()
            ->assertJsonPath('data.0.id', 56)
            ->assertJsonPath('data.0.name', 'Max Muster');
    }

    protected function loginRemotely(?int $mglnr = 90100, ?string $password = 'secret'): TestResponse
    {
        return $this->postJson(route('remote.nami.token'), [
            'mglnr' => $mglnr,
            'password' => $password,
        ]);
    }
}
