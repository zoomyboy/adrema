<?php

namespace App\Payment;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated
 */
class Status extends Model
{
    use HasFactory;

    public $fillable = ['name', 'is_bill', 'is_remember'];
    public $timestamps = false;
    public $casts = [
        'is_bill' => 'boolean',
        'is_remember' => 'boolean',
    ];

    public static function default(): int
    {
        return static::where('is_bill', true)->where('is_remember', true)->first()->id;
    }

    public function isAccepted(): bool
    {
        return false === $this->is_bill && false === $this->is_remember;
    }

    // ---------------------------------- Scopes -----------------------------------
    /**
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeNeedsPayment(Builder $query): Builder
    {
        return $query->where(function (Builder $query): Builder {
            return $query->where('is_bill', true)->orWhere('is_remember', true);
        });
    }

    /**
     * @return array<int, array{name: string, id: int}>
     */
    public static function forSelect(): array
    {
        return static::select('name', 'id')->get()->toArray();
    }
}
