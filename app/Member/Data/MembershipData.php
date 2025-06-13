<?php

namespace App\Member\Data;

use Spatie\LaravelData\Data;
use App\Lib\Data\DateData;
use App\Lib\Data\RecordData;
use App\Member\Membership;

class MembershipData extends Data
{

    public function __construct(
        public int $id,
        public RecordData $activity,
        public ?RecordData $subactivity,
        public RecordData $group,
        public ?DateData $promisedAt,
        public DateData $from,
        public bool $isActive,
        public array $links,
    ) {}

    public static function fromModel(Membership $membership): static
    {
        return static::factory()->withoutMagicalCreation()->from([
            'id' => $membership->id,
            'activity' => $membership->activity,
            'subactivity' => $membership->subactivity,
            'isActive' => $membership->isActive(),
            'from' => $membership->from,
            'group' => $membership->group,
            'promisedAt' => $membership->promised_at,
            'links' => [
                'update' => route('membership.update', $membership),
                'destroy' => route('membership.destroy', $membership),
            ]
        ]);
    }

}
