<?php

namespace App\Invoice\Queries;

use App\Member\Member;
use Illuminate\Database\Eloquent\Builder;

class SingleMemberQuery extends InvoiceMemberQuery
{
    public function __construct(
        private Member $member
    ) {
    }

    /**
     * @return Builder<Member>
     */
    protected function getQuery(): Builder
    {
        return Member::where($this->member->only(['lastname', 'address', 'zip', 'location']));
    }
}
