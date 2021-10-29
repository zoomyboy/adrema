<?php

namespace App\Payment;

use App\Member\Member;
use App\Payment\Status;
use App\Payment\Subscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public $fillable = ['member_id', 'subscription_id', 'nr', 'status_id', 'last_remembered_at'];

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function subscription() {
        return $this->belongsTo(Subscription::class);
    }

    public function status() {
        return $this->belongsTo(Status::class);
    }

    public function scopeWhereNeedsPayment($q) {
        return $q->whereHas('status', function($q) {
            return $q->needsPayment();
        });
    }

    public function scopeWhereNeedsBill($q) {
        return $q->whereHas('status', function($q) {
            return $q->where('is_bill', true);
        });
    }

    public function scopeWhereNeedsRemember($q) {
        return $q->whereHas('status', function($q) {
            return $q->where('is_remember', true);
        })->where(fn ($query) => $query->whereNull('last_remembered_at')->orWhere('last_remembered_at', '<=', now()->subMonths(6)));
    }
}
