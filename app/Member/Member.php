<?php

namespace App\Member;

use App\Activity;
use App\Bill\BillKind;
use App\Confession;
use App\Country;
use App\Course\Models\CourseMember;
use App\Group;
use App\Nationality;
use App\Payment\Payment;
use App\Payment\Subscription;
use App\Region;
use App\Setting\NamiSettings;
use App\Subactivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Zoomyboy\LaravelNami\Api;

/**
 * @property string         $subscription_name
 * @property int            $pending_payment
 * @property bool           $is_confirmed
 * @property string         $age_group_icon
 * @property \Carbon\Carbon $try_created_at
 */
class Member extends Model
{
    use Notifiable;
    use HasFactory;

    public $fillable = ['firstname', 'lastname', 'nickname', 'other_country', 'birthday', 'joined_at', 'send_newspaper', 'address', 'further_address', 'zip', 'location', 'main_phone', 'mobile_phone', 'work_phone', 'fax', 'email', 'email_parents', 'nami_id', 'group_id', 'letter_address', 'country_id', 'way_id', 'nationality_id', 'subscription_id', 'region_id', 'gender_id', 'confession_id', 'letter_address', 'bill_kind_id', 'version', 'first_subactivity_id', 'first_activity_id', 'confirmed_at', 'children_phone', 'efz', 'ps_at', 'more_ps_at', 'has_svk', 'has_vk', 'without_education_at', 'without_efz_at', 'multiply_pv', 'multiply_more_pv'];

    public $dates = ['try_created_at', 'joined_at', 'birthday'];

    public $casts = [
        'pending_payment' => 'integer',
        'send_newspaper' => 'boolean',
        'gender_id' => 'integer',
        'way_id' => 'integer',
        'country_id' => 'integer',
        'region_id' => 'integer',
        'confession_id' => 'integer',
        'nami_id' => 'integer',
        'is_confirmed' => 'boolean',
        'has_svk' => 'boolean',
        'has_vk' => 'boolean',
        'multiply_pv' => 'boolean',
        'multiply_more_pv' => 'boolean',
    ];

    public function scopeSearch(Builder $q, ?string $text): Builder
    {
        if (is_null($text)) {
            return $q;
        }

        return $q->where('firstname', 'LIKE', '%'.$text.'%')
             ->orWhere('lastname', 'LIKE', '%'.$text.'%')
             ->orWhere('address', 'LIKE', '%'.$text.'%')
             ->orWhere('zip', 'LIKE', '%'.$text.'%')
             ->orWhere('location', 'LIKE', '%'.$text.'%');
    }

    // ---------------------------------- Actions ----------------------------------
    public function syncVersion(): void
    {
        $version = app(NamiSettings::class)->login()->member($this->group->nami_id, $this->nami_id)['version'];

        $this->update(['version' => $version]);
    }

    public function createPayment(array $attributes): void
    {
        $this->payments()->create(array_merge($attributes, [
            'last_remembered_at' => now(),
        ]));
    }

    //----------------------------------- Getters -----------------------------------
    public function getFullnameAttribute(): string
    {
        return $this->firstname.' '.$this->lastname;
    }

    public function getEfzLink(): ?string
    {
        return $this->memberships()->whereHas('activity', fn (Builder $query) => $query->where('has_efz', true))->exists()
            ? route('efz', ['member' => $this])
            : null;
    }

    public function getHasNamiAttribute(): bool
    {
        return null !== $this->nami_id;
    }

    public function getNamiMemberships(Api $api): array
    {
        return $api->group($this->group->nami_id)->member($this->nami_id)->memberships()->toArray();
    }

    public function getNamiFeeId(): ?int
    {
        if (!$this->subscription) {
            return null;
        }

        return $this->subscription->fee->nami_id;
    }

    //---------------------------------- Relations ----------------------------------
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(\App\Gender::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function confession(): BelongsTo
    {
        return $this->belongsTo(Confession::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('nr');
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function billKind(): BelongsTo
    {
        return $this->belongsTo(BillKind::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function firstActivity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'first_activity_id');
    }

    public function firstSubActivity(): BelongsTo
    {
        return $this->belongsTo(Subactivity::class, 'first_subactivity_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(CourseMember::class);
    }

    public static function booted()
    {
        static::deleting(function (self $model): void {
            $model->payments->each->delete();
        });
    }

    // ---------------------------------- Scopes -----------------------------------
    public function scopeWithIsConfirmed(Builder $q): Builder
    {
        return $q->selectSub('DATEDIFF(NOW(), IFNULL(confirmed_at, DATE_SUB(NOW(), INTERVAL 3 YEAR))) < 712', 'is_confirmed');
    }

    public function scopeWithSubscriptionName(Builder $q): Builder
    {
        return $q->addSelect([
            'subscription_name' => Subscription::select('name')->whereColumn('subscriptions.id', 'members.subscription_id')->limit(1),
        ]);
    }

    public function scopeWithPendingPayment(Builder $q): Builder
    {
        return $q->addSelect([
            'pending_payment' => Payment::selectRaw('SUM(subscriptions.amount)')
                ->whereColumn('payments.member_id', 'members.id')
                ->whereNeedsPayment()
                ->join('subscriptions', 'subscriptions.id', 'payments.subscription_id'),
        ]);
    }

    public function scopeWithAgeGroup(Builder $q): Builder
    {
        return $q->addSelect([
            'age_group_icon' => Subactivity::select('slug')
                ->join('memberships', 'memberships.subactivity_id', 'subactivities.id')
                ->where('subactivities.is_age_group', true)
                ->whereColumn('memberships.member_id', 'members.id')
                ->limit(1),
        ]);
    }

    public function scopeWhereHasPendingPayment(Builder $q): Builder
    {
        return $q->whereHas('payments', function (Builder $q): void {
            $q->whereNeedsPayment();
        });
    }

    public function scopeWhereAusstand(Builder $q): Builder
    {
        return $q->whereHas('payments', function ($q) {
            return $q->whereHas('status', fn ($q) => $q->where('is_remember', true));
        });
    }

    public function scopePayable(Builder $q): Builder
    {
        return $q->where('bill_kind_id', '!=', null)->where('subscription_id', '!=', null);
    }

    public function scopeWhereNoPayment(Builder $q, int $year): Builder
    {
        return $q->whereDoesntHave('payments', function (Builder $q) use ($year) {
            $q->where('nr', '=', $year);
        });
    }

    public function scopeForDashboard(Builder $q): Builder
    {
        return $q->selectRaw('SUM(id)');
    }

    public function scopeFilter(Builder $q, array $filter): Builder
    {
        if (true === data_get($filter, 'ausstand', false)) {
            $q->whereAusstand();
        }
        if (data_get($filter, 'bill_kind', false)) {
            $q->where('bill_kind_id', $filter['bill_kind']);
        }
        if (data_get($filter, 'subactivity_id', false) || data_get($filter, 'activity_id', false)) {
            $q->whereHas('memberships', function ($q) use ($filter) {
                if (data_get($filter, 'subactivity_id', false)) {
                    $q->where('subactivity_id', $filter['subactivity_id']);
                }
                if (data_get($filter, 'activity_id', false)) {
                    $q->where('activity_id', $filter['activity_id']);
                }
            });
        }

        return $q;
    }

    public function scopeEndingTries(Builder $q): Builder
    {
        return $q->whereHas('memberships', fn ($q) => $q
            ->where('created_at', '<=', now()->subWeeks(7))
            ->whereHas('activity', fn ($q) => $q->where('is_try', true)))
            ->addSelect([
                'try_created_at' => Membership::select('created_at')
                    ->whereColumn('memberships.member_id', 'members.id')
                    ->join('activities', 'activities.id', 'memberships.activity_id')
                    ->where('activities.is_try', true),
            ]);
    }
}
