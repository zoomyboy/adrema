<?php

namespace App\Letter\Queries;

use App\Letter\BillKind;
use App\Member\Member;
use Illuminate\Database\Eloquent\Builder;

class BillKindQuery extends LetterMemberQuery
{
    public function __construct(
        private BillKind $billKind
    ) {
    }

    /**
     * @return Builder<Member>
     */
    protected function getQuery(): Builder
    {
        return Member::where('bill_kind', $this->billKind);
    }
}
