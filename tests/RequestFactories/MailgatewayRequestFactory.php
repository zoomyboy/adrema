<?php

namespace Tests\RequestFactories;

use App\Mailgateway\Types\Type;
use Worksome\RequestFactories\RequestFactory;

class MailgatewayRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(5, true),
            'type' => [
                'cls' => app('mail-gateways')->random(),
                'params' => [],
            ],
            'domain' => $this->faker->safeEmailDomain(),
        ];
    }

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }

    public function domain(string $domain): self
    {
        return $this->state(['domain' => $domain]);
    }

    /**
     * @param class-string<Type>   $type
     * @param array<string, mixed> $params
     */
    public function type(string $type, array $params): self
    {
        return $this->state(['type' => [
            'cls' => $type,
            'params' => $params,
        ]]);
    }

    public function missingType(): self
    {
        return $this->state(['type' => [
            'cls' => null,
            'params' => [],
        ]]);
    }

    public function withoutName(): self
    {
        return $this->state(['name' => '']);
    }

    public function withoutDomain(): self
    {
        return $this->state(['domain' => '']);
    }
}
