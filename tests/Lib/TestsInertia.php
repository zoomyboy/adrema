<?php

namespace Tests\Lib;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;

trait TestsInertia
{
    /**
     * @param mixed   $value
     * @param ?string $key
     */
    public function assertInertiaHas($value, TestResponse $response, ?string $key = null): void
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

    /**
     * @param mixed $should
     * @param mixed $is
     */
    public function assertInertiaDeepNest($should, $is): void
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

    /**
     * @return mixed
     */
    public function inertia(TestResponse $response, string $key)
    {
        return data_get($response->viewData('page')['props'], $key);
    }
}
