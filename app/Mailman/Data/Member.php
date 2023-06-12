<?php

namespace App\Mailman\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class Member extends Data
{
    public function __construct(
        public string $email,
        public string $listId,
        public string $memberId,
    ) {
    }
}
