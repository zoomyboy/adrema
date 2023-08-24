<?php

namespace App\Member;

use App\Activity;
use App\Group;
use App\Nami\HasNamiField;
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
    use HasNamiField;

    /** @var array<int, string> */
    public $guarded = [];

    /** @var array<string, string> */
    public $casts = [
        'from' => 'date',
        'to' => 'date',
        'promised_at' => 'date',
    ];

    /**
     * @return BelongsTo<Activity, self>
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * @return BelongsTo<Group, self>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @return BelongsTo<Subactivity, self>
     */
    public function subactivity(): BelongsTo
    {
        return $this->belongsTo(Subactivity::class);
    }

    /**
     * @return BelongsTo<Member, self>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @param Builder<Membership> $query
     *
     * @return Builder<Membership>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('to');
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
        return $query->active()->whereHas('activity', fn ($builder) => $builder->where('is_try', true));
    }
}
