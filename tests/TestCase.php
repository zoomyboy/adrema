<?php

namespace Tests;

use App\Group;
use App\Member\Member;
use App\Setting\NamiSettings;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\AssertableJsonString;
use Illuminate\Testing\TestResponse;
use Phake;
use PHPUnit\Framework\Assert;
use Tests\Lib\MakesHttpCalls;
use Tests\Lib\TestsInertia;
use Zoomyboy\LaravelNami\Authentication\Auth;
use Zoomyboy\TableDocument\TestsExcelDocuments;

class TestCase extends BaseTestCase
{
    use TestsInertia;
    use MakesHttpCalls;
    use TestsExcelDocuments;

    protected User $me;

    protected function setUp(): void
    {
        parent::setUp();
        Auth::fake();
        Member::disableGeolocation();
        $this->initInertiaTestcase();
    }

    public function loginNami(int $mglnr = 12345, string $password = 'password', int|Group $groupId = 55): static
    {
        Auth::success($mglnr, $password);
        $group = is_int($groupId)
            ? Group::factory()->create(['nami_id' => $groupId])
            : $groupId;

        $this->withNamiSettings($mglnr, $password, $group->nami_id);

        return $this;
    }

    public function withNamiSettings(int $mglnr = 12345, string $password = 'password', int $groupId = 55): self
    {
        NamiSettings::fake([
            'mglnr' => $mglnr,
            'password' => $password,
            'default_group_id' => $groupId,
        ]);

        return $this;
    }

    public function login(): static
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
            $this->assertTrue($sessionErrors->has($key), "Cannot find key {$key} in errors '" . print_r($sessionErrors, true));
            $this->assertEquals($value, $sessionErrors->get($key)[0], "Failed to validate value for session error key {$key}. Actual value: " . print_r($sessionErrors, true));
        }

        return $this;
    }

    /**
     * @template M of object
     * @param class-string<M> $class
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

    public function initInertiaTestcase(): void
    {
        TestResponse::macro('assertInertiaPath', function ($path, $value) {
            /** @var TestResponse */
            $response = $this;
            $props = data_get($response->viewData('page'), 'props');
            Assert::assertNotNull($props);
            $json = new AssertableJsonString($props);
            $json->assertPath($path, $value);
            return $this;
        });

        TestResponse::macro('assertInertiaCount', function ($path, $count) {
            /** @var TestResponse */
            $response = $this;
            $props = data_get($response->viewData('page'), 'props');
            Assert::assertNotNull($props);
            $json = new AssertableJsonString($props);
            $json->assertCount($count, $path);
            return $this;
        });

        TestResponse::macro('assertComponent', function (string $component) {
            /** @var TestResponse */
            $response = $this;
            Assert::assertEquals($component, data_get($response->viewData('page'), 'component'));
            return $this;
        });

        TestResponse::macro('assertPdfPageCount', function (int $count) {
            /** @var TestResponse */
            $response = $this;
            $file = $response->getFile();
            Assert::assertTrue(file_exists($file->getPathname()));
            exec('pdfinfo ' . escapeshellarg($file->getPathname()) . ' | grep ^Pages | sed "s/Pages:\s*//"', $output, $returnVar);

            Assert::assertSame(0, $returnVar, 'Failed to get Pages of PDF File ' . $file->getPathname());
            Assert::assertCount(1, $output, 'Failed to parse output format of pdfinfo');
            Assert::assertEquals($count, $output[0]);

            return $this;
        });

        TestResponse::macro('assertPdfName', function (string $filename) {
            /** @var TestResponse */
            $response = $this;
            Assert::assertEquals($filename, $response->getFile()->getFilename());

            return $this;
        });

        TestResponse::macro('assertHasJsonPath', function (string $path) {
            /** @var TestResponse */
            $response = $this;
            Assert::assertTrue(Arr::has($response->json(), $path), 'Failed that key ' . $path . ' is in Response.');

            return $this;
        });
    }
}
