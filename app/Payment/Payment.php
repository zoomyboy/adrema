<?php

namespace App\Payment;

use App\Member\Member;
use App\Payment\Status;
use App\Payment\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public $fillable = ['member_id', 'subscription_id', 'nr', 'status_id', 'last_remembered_at'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function scopeWhereNeedsPayment(Builder $q): Builder
    {
        return $q->whereHas('status', function($q) {
            return $q->needsPayment();
        });
    }

    public function scopeWhereNeedsBill(Builder $q): Builder
    {
        return $q->whereHas('status', function($q) {
            return $q->where('is_bill', true);
        });
    }

    public function scopeWhereNeedsRemember(Builder $q): Builder
    {
        return $q->whereHas('status', function($q) {
            return $q->where('is_remember', true);
        })->where(fn ($query) => $query->whereNull('last_remembered_at')->orWhere('last_remembered_at', '<=', now()->subMonths(3)));
    }
}
