<?php

namespace App\Invoice\Models;

use App\Invoice\Enums\InvoiceStatus;
use App\Member\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'to' => 'json',
    ];

    /**
     * @return HasMany<InvoicePosition>
     */
    public function positions(): HasMany
    {
        return $this->hasMany(InvoicePosition::class);
    }

    public static function createForMember(Member $member): self
    {
        return static::create([
            'to' => [
                'name' => 'Familie ' . $member->lastname,
                'address' => $member->address,
                'zip' => $member->zip,
                'location' => $member->location,
            ],
            'greeting' => 'Liebe Familie ' . $member->lastname,
            'status' => InvoiceStatus::NEW,
        ]);
    }
}
