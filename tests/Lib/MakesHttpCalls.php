<?php

namespace Tests\Lib;

use Illuminate\Testing\TestResponse;

trait MakesHttpCalls
{
    /**
     * @param array<string, mixed> $filter
     */
    public function callFilter(string $routeName, array $filter): TestResponse
    {
        return $this->call('GET', $this->filterUrl($routeName, $filter));
    }

    /**
     * @param array<string, mixed> $filter
     */
    public function filterUrl(string $routeName, array $filter): string
    {
        $params = [
            'filter' => $this->filterString($filter),
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
