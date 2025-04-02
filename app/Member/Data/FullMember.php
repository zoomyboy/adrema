<?php

namespace App\Member\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Zoomyboy\LaravelNami\Data\Course as NamiCourse;
use Zoomyboy\LaravelNami\Data\Member as NamiMember;
use Zoomyboy\LaravelNami\Data\MembershipEntry as NamiMembershipEntry;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class FullMember extends Data
{
    /**
     * @param DataCollection<int, NamiCourse> $courses
     * @param DataCollection<int, NamiMembershipEntry> $memberships
     */
    public function __construct(
        public NamiMember $member,
        #[DataCollectionOf(NamiCourse::class)]
        public DataCollection $courses,
        #[DataCollectionOf(NamiMembershipEntry::class)]
        public DataCollection $memberships,
    ) {
    }
}
