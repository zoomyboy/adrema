<?php

namespace Tests\Lib;

use Illuminate\Testing\TestResponse;

trait MakesHttpCalls
{
    /**
     * @param array<string, mixed> $filter
     * @param array<string, mixed> $routeParams
     */
    public function callFilter(string $routeName, array $filter, array $routeParams = []): TestResponse
    {
        return $this->call('GET', $this->filterUrl($routeName, $filter, $routeParams));
    }

    /**
     * @param array<string, mixed> $filter
     * @param array<string, mixed> $routeParams
     */
    public function filterUrl(string $routeName, array $filter, array $routeParams = []): string
    {
        $params = [
            'filter' => $this->filterString($filter),
            ...$routeParams
        ];

        return route($routeName, $params);
    }

    /**
     * @param array<string, mixed> $filter
     */
    public function filterString(array $filter): string
    {
        return base64_encode(rawurlencode(json_encode($filter)));
    }
}
