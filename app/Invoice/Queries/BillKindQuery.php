<?php

namespace App\Invoice\Queries;

use App\Invoice\BillKind;
use App\Member\Member;
use Illuminate\Database\Eloquent\Builder;

class BillKindQuery extends InvoiceMemberQuery
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
