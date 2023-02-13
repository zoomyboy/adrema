<?php

namespace App\Initialize\Actions;

use App\Actions\InsertCoursesAction;
use App\Actions\InsertMemberAction;
use App\Actions\InsertMembershipsAction;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Data\Course as NamiCourse;
use Zoomyboy\LaravelNami\Data\Member as NamiMember;
use Zoomyboy\LaravelNami\Data\MembershipEntry as NamiMembershipEntry;

class ProcessRedisAction
{
    use AsAction;

    public string $jobQueue = 'single';

    /**
     * @param array{member: array<string, mixed>, memberships: array<string, mixed>, courses: array<string, mixed>} $data
     */
    public function handle(array $data): void
    {
        $localMember = InsertMemberAction::run(NamiMember::from($data['member']));
        InsertMembershipsAction::run(
            $localMember,
            collect($data['memberships'])->map(fn ($membership) => NamiMembershipEntry::from($membership)),
        );
        InsertCoursesAction::run(
            $localMember,
            collect($data['courses'])->map(fn ($course) => NamiCourse::from($course)),
        );
    }
}
