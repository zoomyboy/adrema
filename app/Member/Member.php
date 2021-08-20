<?php

namespace App\Member;

use App\Activity;
use App\Bill\BillKind;
use App\Confession;
use App\Country;
use App\Group;
use App\Nationality;
use App\Payment\Payment;
use App\Payment\Subscription;
use App\Region;
use App\Subactivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Zoomyboy\LaravelNami\Api;

class Member extends Model
{
    use Notifiable;
    use HasFactory;

    public $fillable = ['firstname', 'lastname', 'nickname', 'other_country', 'birthday', 'joined_at', 'send_newspaper', 'address', 'further_address', 'zip', 'location', 'main_phone', 'mobile_phone', 'work_phone', 'fax', 'email', 'email_parents', 'nami_id', 'group_id', 'letter_address', 'country_id', 'way_id', 'nationality_id', 'subscription_id', 'region_id', 'gender_id', 'confession_id', 'letter_address', 'bill_kind_id', 'version', 'first_subactivity_id', 'first_activity_id', 'confirmed_at', 'children_phone'];

    public $dates = ['joined_at', 'birthday'];

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
    ];

    public function scopeSearch(Builder $q, ?string $text): Builder {
        if (is_null($text)) { return $q; }
        return $q->where('firstname', 'LIKE', '%'.$text.'%')
             ->orWhere('lastname', 'LIKE', '%'.$text.'%')
             ->orWhere('address', 'LIKE', '%'.$text.'%')
             ->orWhere('zip', 'LIKE', '%'.$text.'%')
             ->orWhere('location', 'LIKE', '%'.$text.'%');
    }


    //----------------------------------- Getters -----------------------------------
    public function getFullnameAttribute(): string {
        return $this->firstname.' '.$this->lastname;
    }

    public function getHasNamiAttribute(): bool {
        return $this->nami_id !== null;
    }

    public function getNamiMemberships(Api $api): array {
        return $api->group($this->group->nami_id)->member($this->nami_id)->memberships()->toArray();
    }

    public function getNamiFeeId(): ?int {
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

    public static function booted()
    {
        static::deleting(function(self $model): void {
            $model->payments->each->delete();
        });
    }

    // ---------------------------------- Scopes -----------------------------------
    public function scopeWithIsConfirmed(Builder $q): Builder {
        return $q->selectSub('DATEDIFF(NOW(), IFNULL(confirmed_at, DATE_SUB(NOW(), INTERVAL 3 YEAR))) < 712', 'is_confirmed');
    }

    public function scopeWithSubscriptionName(Builder $q): Builder {
        return $q->addSelect([
            'subscription_name' => Subscription::select('name')->whereColumn('subscriptions.id', 'members.subscription_id')->limit(1)
        ]);
    }

    public function scopeWithPendingPayment(Builder $q): Builder {
        return $q->addSelect([
            'pending_payment' => Payment::selectRaw('SUM(subscriptions.amount)')
                ->whereColumn('payments.member_id', 'members.id')
                ->whereNeedsPayment()
                ->join('subscriptions', 'subscriptions.id', 'payments.subscription_id')
        ]);
    }

    public function scopeWhereHasPendingPayment(Builder $q): Builder {
        return $q->whereHas('payments', function(Builder $q): void {
            $q->whereNeedsPayment();
        });
    }

    public function scopePayable(Builder $q): Builder {
        return $q->where('bill_kind_id', '!=', null)->where('subscription_id', '!=', null);
    }

    public function scopeWhereNoPayment(Builder $q, int $year): Builder {
        return $q->whereDoesntHave('payments', function(Builder $q) use ($year) {
            $q->where('nr', '=', $year);
        });
    }

    public function scopeForDashboard(Builder $q): Builder {
        return $q->selectRaw('SUM(id)');
    }

}
