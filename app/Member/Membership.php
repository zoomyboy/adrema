<?php

namespace App\Member;

use App\Activity;
use App\Subactivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    use HasFactory;

    public $fillable = ['subactivity_id', 'activity_id', 'group_id', 'member_id', 'nami_id', 'from'];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function subactivity(): BelongsTo
    {
        return $this->belongsTo(Subactivity::class);
    }

}
