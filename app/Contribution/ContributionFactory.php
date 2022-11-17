<?php

namespace App\Contribution;

use App\Contribution\Documents\ContributionDocument;
use App\Contribution\Documents\DvDocument;
use App\Contribution\Documents\SolingenDocument;

class ContributionFactory
{
    /**
     * @var array<int, class-string<ContributionDocument>>
     */
    private array $documents = [
        DvDocument::class,
        SolingenDocument::class,
    ];

    /**
     * @return array<int, array{id: string, name: string}>
     */
    public function compilerSelect(): array
    {
        return collect($this->documents)->map(fn ($document) => [
            'title' => $document::getName(),
            'class' => $document,
        ])->toArray();
    }
}
