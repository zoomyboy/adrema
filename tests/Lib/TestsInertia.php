<?php

namespace Tests\Lib;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;

trait TestsInertia {

    public function assertInertiaHas(mixed $value, TestResponse $response, ?string $key = null): void
    {
        $bindings = json_decode(json_encode($value), true);
        $viewData = json_decode(json_encode(
            data_get($response->viewData('page')['props'], $key)
        ), true);
        $bindings = is_array($bindings) ? $bindings : [$bindings];
        $viewData = is_array($viewData) ? $viewData : [$viewData];
        $this->assertInertiaDeepNest($bindings, $viewData);
    }

    public function assertComponent(string $component, TestResponse $response): void
    {
        PHPUnit::assertEquals($component, $response->viewData('page')['component']);
    }

    public function assertInertiaDeepNest(mixed $should, mixed $is): void
    {
        foreach ($should as $key => $value) {
            PHPUnit::assertArrayHasKey($key, $is);

            if (is_array($value)) {
                $this->assertInertiaDeepNest($value, $is[$key]);
                continue;
            }

            PHPUnit::assertSame($value, $is[$key]);
        }
    }

    public function inertia(TestResponse $response, string $key): mixed
    {
        return data_get($response->viewData('page')['props'], $key);
    }

}
