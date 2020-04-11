<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public $fillable = ['name', 'nami_id'];

    public $timestamps = false;

    public function activities() {
        return $this->belongsToMany(Activity::class);
    }
}
