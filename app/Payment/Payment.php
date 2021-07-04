<?php

namespace App\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Member\Member;
use App\Payment\Status;
use App\Payment\Subscription;

class Payment extends Model
{
    use HasFactory;

    public $fillable = ['member_id', 'subscription_id', 'nr', 'status_id'];

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
}
