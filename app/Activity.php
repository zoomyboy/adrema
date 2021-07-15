<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{

    public $fillable = ['name', 'nami_id'];
    public $timestamps = false;

    public $casts = [
        'nami_id' => 'integer'
    ];

    public function subactivities(): BelongsToMany {
        return $this->belongsToMany(Subactivity::class);
    }

    public static function nami(int $id): ?self {
        return static::firstWhere('nami_id', $id);
    }
}
