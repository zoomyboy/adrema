<?php

namespace App\Invoice\Models;

use App\Member\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePosition extends Model
{
    use HasFactory;

    public $guarded = [];

    /**
     * @return BelongsTo<Member>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
