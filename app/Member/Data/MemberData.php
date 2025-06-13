<?php

namespace App\Member\Data;

use Spatie\LaravelData\Data;
use App\Member\Member;

class MemberData extends Data
{

    public function __construct(
        public string $fullname,
    ) {}

    public static function fromModel(Member $member): static
    {
        return static::factory()->withoutMagicalCreation()->from([
            'fullname' => $member->fullname
        ]);
    }

}
