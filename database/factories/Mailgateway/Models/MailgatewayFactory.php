<?php

namespace Database\Factories\Mailgateway\Models;

use App\Mailgateway\Models\Mailgateway;
use App\Mailgateway\Types\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Mailgateway\Models\Mailgateway>
 */
class MailgatewayFactory extends Factory
{
    protected $model = Mailgateway::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
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

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }

    public function domain(string $domain): self
    {
        return $this->state(['domain' => $domain]);
    }
}
