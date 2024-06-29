<?php

namespace App\Contribution;

use App\Contribution\Documents\BdkjHesse;
use App\Contribution\Documents\ContributionDocument;
use App\Contribution\Documents\RdpNrwDocument;
use App\Contribution\Documents\CityRemscheidDocument;
use App\Contribution\Documents\CitySolingenDocument;
use App\Contribution\Documents\CityFrankfurtMainDocument;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class ContributionFactory
{
    /**
     * @var array<int, class-string<ContributionDocument>>
     */
    private array $documents = [
        RdpNrwDocument::class,
        CitySolingenDocument::class,
        CityRemscheidDocument::class,
        CityFrankfurtMainDocument::class,
        BdkjHesse::class,
    ];

    /**
     * @return Collection<int, array{title: string, class: class-string<ContributionDocument>}>
     */
    public function compilerSelect(): Collection
    {
        return collect($this->documents)->map(fn ($document) => [
            'title' => $document::getName(),
            'class' => $document,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function typeRule(): array
    {
        return [
            'type' => ['required', Rule::in($this->documents)],
        ];
    }

    /**
     * @param class-string<ContributionDocument> $type
     *
     * @return array<string, mixed>
     */
    public function rules(string $type): array
    {
        return [
            ...$type::globalRules(),
            ...$type::rules(),
        ];
    }
}
