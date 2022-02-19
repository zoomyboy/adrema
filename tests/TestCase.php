<?php

namespace Tests;

use App\Member\Member;
use App\Setting\GeneralSettings;
use App\Setting\NamiSettings;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Testing\TestResponse;
use Tests\Lib\TestsInertia;
use Zoomyboy\LaravelNami\Authentication\Auth;
use Zoomyboy\LaravelNami\Backend\FakeBackend;
use Zoomyboy\LaravelNami\Nami;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use TestsInertia;

    protected User $me;

    public function setUp(): void
    {
        parent::setUp();
        Auth::fake();
    }

    public function loginNami(int $mglnr = 12345, string $password = 'password'): self
    {
        Auth::success($mglnr, $password);
        NamiSettings::fake([
            'mglnr' => $mglnr,
            'password' => $password,
        ]);

        return $this;
    }

    public function failedNami(int $mglnr = 12345, string $password = 'password'): self
    {
        Auth::failed($mglnr, $password);
        NamiSettings::fake([
            'mglnr' => $mglnr,
            'password' => $password,
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
    public function assertErrors(array $errors, TestResponse $response) {
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

}
