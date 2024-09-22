<?php

namespace App\Invoice\Models;

use App\Member\Member;
use Database\Factories\Invoice\Models\InvoicePositionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePosition extends Model
{
    /** @use HasFactory<InvoicePositionFactory> */
    use HasFactory;

    public $guarded = [];

    /**
     * @return BelongsTo<Member, self>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @return BelongsTo<Invoice, self>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public static function booted(): void
    {
        static::saved(function ($model) {
            $model->member->touch();
        });
        static::deleted(function ($model) {
            if ($model->invoice->positions()->get()->count() === 0) {
                $model->invoice->delete();
            }
            $model->member->touch();
        });
    }
}
