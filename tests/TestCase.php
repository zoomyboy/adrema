<?php

namespace Tests;

use App\Member\Member;
use App\Setting\GeneralSettings;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Tests\Lib\TestsInertia;
use Zoomyboy\LaravelNami\Backend\FakeBackend;
use Zoomyboy\LaravelNami\Nami;
use Zoomyboy\LaravelNami\NamiUser;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use TestsInertia;

    public function fakeAuthUser(): void
    {
        app(FakeBackend::class)
            ->fakeLogin('123')
            ->addSearch(123, ['entries_vorname' => '::firstname::', 'entries_nachname' => '::lastname::', 'entries_gruppierungId' => 1000]);
    }

    public function login(): self
    {
        $this->fakeAuthUser();
        auth()->loginNami([
            'mglnr' => 123,
            'password' => 'secret',
        ]);

        return $this;
    }

    public function init(): self
    {
        Member::factory()->defaults()->create();

        return $this;
    }

}
