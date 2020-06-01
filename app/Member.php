<?php

namespace App;

use App\Collections\OwnCollection;
use App\Events\MemberCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Member extends Model
{
    use Notifiable;

    public $fillable = ['firstname', 'lastname', 'nickname', 'other_country', 'birthday', 'joined_at', 'keepdata', 'sendnewspaper', 'address', 'further_address', 'zip', 'city', 'phone', 'mobile', 'business_phone', 'fax', 'email', 'email_parents', 'nami_id', 'active', 'letter_address', 'country_id', 'way_id', 'nationality_id', 'subscription_id', 'region_id', 'gender_id', 'confession_id'];

    public $dates = ['joined_at', 'birthday'];

    public $casts = [
        'active' => 'boolean',
        'keepdata' => 'boolean',
        'sendnewspaper' => 'boolean',
        'gender_id' => 'integer',
        'way_id' => 'integer',
        'country_id' => 'integer',
        'region_id' => 'integer',
        'confession_id' => 'integer',
        'nami_id' => 'integer',
    ];

    public function newCollection(array $models = [])
    {
        return new OwnCollection($models);
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
        return $this->belongsTo(Way::class);
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
}
