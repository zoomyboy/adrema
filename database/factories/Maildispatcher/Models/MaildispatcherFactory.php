<?php

namespace Database\Factories\Maildispatcher\Models;

use App\Maildispatcher\Models\Maildispatcher;
use App\Member\FilterScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Maildispatcher\Models\Maildispatcher>
 */
class MaildispatcherFactory extends Factory
{
    public $model = Maildispatcher::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => Str::slug($this->faker->words(3, true)),
        ];
    }

    public function filter(FilterScope $filter): self
    {
        return $this->state([
            'filter' => $filter->toArray(),
        ]);
    }
}
