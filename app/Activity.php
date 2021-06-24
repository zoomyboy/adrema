<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    public $fillable = ['name', 'nami_id'];

    public $timestamps = false;

    public $casts = [
        'nami_id' => 'integer'
    ];

    public function subactivities() {
        return $this->belongsToMany(Subactivity::class);
    }

    public static function nami($id) {
        return static::firstWhere('nami_id', $id);
    }
}
