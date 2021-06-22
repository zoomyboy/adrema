<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subactivity extends Model
{
    use HasFactory;

    public $fillable = ['name', 'nami_id'];

    public $timestamps = false;

    public function activities() {
        return $this->belongsToMany(Activity::class);
    }
}
