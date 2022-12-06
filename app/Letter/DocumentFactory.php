<?php

namespace App\Letter;

use App\Letter\Queries\LetterMemberQuery;
use Illuminate\Support\Collection;

class DocumentFactory
{
    /**
     * @var array<int, class-string<Letter>>
     */
    private array $types = [
        BillDocument::class,
        RememberDocument::class,
    ];

    /**
     * @return Collection<int, class-string<Letter>>
     */
    public function getTypes(): Collection
    {
        return collect($this->types);
    }

    /**
     * @param class-string<Letter> $type
     */
    public function singleLetter(string $type, LetterMemberQuery $query): ?Letter
    {
        $pages = $query->getPages($type);

        if ($pages->isEmpty()) {
            return null;
        }

        return $this->resolve($type, $pages);
    }

    /**
     * @param class-string<Letter> $type
     *
     * @return Collection<int, Letter>
     */
    public function letterCollection(string $type, LetterMemberQuery $query): Collection
    {
        return $query
            ->getPages($type)
            ->map(fn ($page) => $this->resolve($type, collect([$page])));
    }

    public function afterSingle(Letter $letter): void
    {
        foreach ($letter->allPayments() as $payment) {
            $letter->afterSingle($payment);
        }
    }

    /**
     * @param class-string<Letter>  $type
     * @param Collection<int, Page> $pages
     */
    private function resolve(string $type, Collection $pages): Letter
    {
        return new $type($pages);
    }
}
