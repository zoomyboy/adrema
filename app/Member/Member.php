<?php

namespace App\Member;

use App\Events\MemberCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Bill\BillKind;

class Member extends Model
{
    use Notifiable;

    public $fillable = ['firstname', 'lastname', 'nickname', 'other_country', 'birthday', 'joined_at', 'send_newspaper', 'address', 'further_address', 'zip', 'location', 'main_phone', 'mobile_phone', 'work_phone', 'fax', 'email', 'email_parents', 'nami_id', 'letter_address', 'country_id', 'way_id', 'nationality_id', 'fee_id', 'region_id', 'gender_id', 'confession_id', 'letter_address', 'bill_kind_id'];

    public $dates = ['joined_at', 'birthday'];

    public $casts = [
        'send_newspaper' => 'boolean',
        'gender_id' => 'integer',
        'way_id' => 'integer',
        'country_id' => 'integer',
        'region_id' => 'integer',
        'confession_id' => 'integer',
        'nami_id' => 'integer',
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
        return $this->hasMany(\App\Payment::class)->orderBy('nr');
    }

    public function way()
    {
        return $this->belongsTo(App\Way::class);
    }

    public function nationality()
    {
        return $this->belongsTo(App\Nationality::class);
    }

    public function memberships()
    {
        return $this->hasMany(App\Membership::class);
    }

    public function fee()
    {
        return $this->belongsTo(App\Fee::class);
    }

    public function billKind() {
        return $this->belongsTo(BillKind::class);
    }

    public static function booted() {
        static::updating(function($model) {
            if ($model->nami_id === null) {
                $model->bill_kind_id = null;
            }
        });
    }
}
