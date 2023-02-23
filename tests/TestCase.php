<?php

namespace Tests;

use App\Group;
use App\Member\Member;
use App\Setting\NamiSettings;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;
use Phake;
use Tests\Lib\MakesHttpCalls;
use Tests\Lib\TestsInertia;
use Zoomyboy\LaravelNami\Authentication\Auth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use TestsInertia;
    use MakesHttpCalls;

    protected User $me;

    public function setUp(): void
    {
        parent::setUp();
        Auth::fake();
    }

    public function loginNami(int $mglnr = 12345, string $password = 'password'): self
    {
        Auth::success($mglnr, $password);
        $this->withNamiSettings($mglnr, $password);
        Group::factory()->create(['nami_id' => 55]);

        return $this;
    }

    public function withNamiSettings(int $mglnr = 12345, string $password = 'password'): self
    {
        NamiSettings::fake([
            'mglnr' => $mglnr,
            'password' => $password,
            'default_group_id' => 55,
        ]);

        return $this;
    }

    public function login(): self
    {
        $this->be($user = User::factory()->create());
        $this->me = $user;

        return $this;
    }

    public function init(): self
    {
        Member::factory()->defaults()->create();

        return $this;
    }

    /**
     * @param array<string, string> $errors
     */
    public function assertErrors(array $errors, TestResponse $response): self
    {
        $response->assertSessionHas('errors');
        $this->assertInstanceOf(RedirectResponse::class, $response->baseResponse);
        /** @var RedirectResponse */
        $response = $response;

        $sessionErrors = $response->getSession()->get('errors')->getBag('default');

        foreach ($errors as $key => $value) {
            $this->assertTrue($sessionErrors->has($key), "Cannot find key {$key} in errors '".print_r($sessionErrors, true));
            $this->assertEquals($value, $sessionErrors->get($key)[0], "Failed to validate value for session error key {$key}. Actual value: ".print_r($sessionErrors, true));
        }

        return $this;
    }

    /**
     * @param <class-string> $class
     */
    public function stubIo(string $class, callable $mocker): self
    {
        $mock = Phake::mock($class);
        $mocker($mock);
        app()->instance($class, $mock);

        return $this;
    }

    public function fakeAllHttp(): self
    {
        Http::fake(['*' => Http::response('', 200)]);

        return $this;
    }
}
