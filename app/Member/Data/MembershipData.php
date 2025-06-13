<?php

namespace App\Member\Data;

use App\Activity;
use App\Group;
use Spatie\LaravelData\Data;
use App\Lib\Data\DateData;
use App\Lib\Data\RecordData;
use App\Lib\HasMeta;
use App\Member\Membership;
use App\Membership\FilterScope;
use App\Subactivity;

class MembershipData extends Data
{

    use HasMeta;

    public function __construct(
        public int $id,
        public RecordData $activity,
        public ?RecordData $subactivity,
        public RecordData $group,
        public ?DateData $promisedAt,
        public DateData $from,
        public ?DateData $to,
        public MemberData $member,
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
            'to' => $membership->to,
            'group' => $membership->group,
            'promisedAt' => $membership->promised_at,
            'member' => $membership->member,
            'links' => [
                'update' => route('membership.update', $membership),
                'destroy' => route('membership.destroy', $membership),
            ]
        ]);
    }

    public static function meta(): array {
        return [
            'activities' => RecordData::collect(Activity::get()),
            'subactivities' => RecordData::collect(Subactivity::get()),
            'groups' => RecordData::collect(Group::get()),
            'filter' => FilterScope::fromRequest(request()->input('filter', '')),
        ];
    }

}
