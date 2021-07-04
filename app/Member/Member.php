<?php

namespace App\Member;

use App\Events\MemberCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Bill\BillKind;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Nationality;
use App\Group;
use App\Activity;
use App\Subactivity;
use Zoomyboy\LaravelNami\NamiUser;
use App\Payment\Subscription;
use App\Payment\Payment;

class Member extends Model
{
    use Notifiable;
    use HasFactory;

    public $fillable = ['firstname', 'lastname', 'nickname', 'other_country', 'birthday', 'joined_at', 'send_newspaper', 'address', 'further_address', 'zip', 'location', 'main_phone', 'mobile_phone', 'work_phone', 'fax', 'email', 'email_parents', 'nami_id', 'group_id', 'letter_address', 'country_id', 'way_id', 'nationality_id', 'subscription_id', 'region_id', 'gender_id', 'confession_id', 'letter_address', 'bill_kind_id', 'version', 'first_subactivity_id', 'first_activity_id', 'confirmed_at', 'children_phone'];

    public $dates = ['joined_at', 'birthday'];

    public $casts = [
        'send_newspaper' => 'boolean',
        'gender_id' => 'integer',
        'way_id' => 'integer',
        'country_id' => 'integer',
        'region_id' => 'integer',
        'confession_id' => 'integer',
        'nami_id' => 'integer',
        'is_confirmed' => 'boolean',
    ];

    public function scopeSearch($q, $text) {
        return $q->where('firstname', 'LIKE', '%'.$text.'%')
             ->orWhere('lastname', 'LIKE', '%'.$text.'%')
             ->orWhere('address', 'LIKE', '%'.$text.'%')
             ->orWhere('zip', 'LIKE', '%'.$text.'%')
             ->orWhere('location', 'LIKE', '%'.$text.'%');
    }


    //----------------------------------- Getters -----------------------------------
    public function getFullnameAttribute() {
        return $this->firstname.' '.$this->lastname;
    }

    public function getHasNamiAttribute() {
        return $this->nami_id !== null;
    }

    public function getNamiMemberships($api) {
        return $api->group($this->group->nami_id)->member($this->nami_id)->memberships()->toArray();
    }

    public function getNamiFeeId() {
        if (!$this->subscription) {
            return null;
        }

        return $this->subscription->fee->nami_id;
    }

    //---------------------------------- Relations ----------------------------------
    public function country()
    {
        return $this->belongsTo(\App\Country::class);
    }

    public function gender()
    {
        return $this->belongsTo(\App\Gender::class);
    }

    public function region()
    {
        return $this->belongsTo(\App\Region::class);
    }

    public function confession()
    {
        return $this->belongsTo(\App\Confession::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->orderBy('nr');
    }

    public function way()
    {
        return $this->belongsTo(App\Way::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function billKind() {
        return $this->belongsTo(BillKind::class);
    }

    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function firstActivity() {
        return $this->belongsTo(Activity::class, 'first_activity_id');
    }

    public function firstSubActivity() {
        return $this->belongsTo(Subactivity::class, 'first_subactivity_id');
    }

    public static function booted() {
        static::updating(function($model) {
            if ($model->nami_id === null) {
                $model->bill_kind_id = null;
            }
        });

        static::deleting(function($model) {
            $model->payments->each->delete();
        });
    }

    // ---------------------------------- Scopes -----------------------------------
    public function scopeWithIsConfirmed($q) {
        $q->selectSub('DATEDIFF(NOW(), IFNULL(confirmed_at, DATE_SUB(NOW(), INTERVAL 3 YEAR))) < 712', 'is_confirmed');
    }

    public function scopeWithSubscriptionName($q) {
        return $q->addSelect([
            'subscription_name' => Subscription::select('name')->whereColumn('subscriptions.id', 'members.subscription_id')->limit(1)
        ]);
    }

}
