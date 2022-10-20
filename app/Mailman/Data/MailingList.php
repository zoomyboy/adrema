<?php

namespace App\Mailman\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class MailingList extends Data
{
    public function __construct(
        public string $description,
        public string $displayName,
        public string $fqdnListname,
        public string $listId,
        public string $listName,
        public string $mailHost,
        public int $memberCount,
        public string $selfLink,
        public int $volume,
    ) {
    }
}
