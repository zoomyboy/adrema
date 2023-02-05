<?php

namespace App\Member;

use App\Activity;
use App\Subactivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $count
 */
class Membership extends Model
{
    use HasFactory;

    public $fillable = ['subactivity_id', 'activity_id', 'group_id', 'member_id', 'nami_id', 'from', 'to', 'promised_at'];

    /**
     * @var array<string, string>
     */
    public $casts = [
        'from' => 'date',
        'to' => 'date',
        'promised_at' => 'date',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function subactivity(): BelongsTo
    {
        return $this->belongsTo(Subactivity::class);
    }

    /**
     * @param Builder<Membership> $query
     *
     * @return Builder<Membership>
     */
    public function scopeIsAgeGroup(Builder $query): Builder
    {
        return $query->whereHas('subactivity', fn ($builder) => $builder->where('is_age_group', true));
    }

    /**
     * @param Builder<Membership> $query
     *
     * @return Builder<Membership>
     */
    public function scopeIsMember(Builder $query): Builder
    {
        return $query->whereHas('activity', fn ($builder) => $builder->where('is_member', true));
    }

    /**
     * @param Builder<Membership> $query
     *
     * @return Builder<Membership>
     */
    public function scopeIsLeader(Builder $query): Builder
    {
        return $query->whereHas('activity', fn ($builder) => $builder->where('has_efz', true));
    }

    /**
     * @param Builder<Membership> $query
     *
     * @return Builder<Membership>
     */
    public function scopeTrying(Builder $query): Builder
    {
        return $query->whereHas('activity', fn ($builder) => $builder->where('is_try', true));
    }
}
