<?php

namespace App\Member;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    public $fillable = ['subactivity_id', 'activity_id', 'group_id', 'member_id', 'nami_id', 'created_at'];
}
