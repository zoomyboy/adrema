<?php

namespace App;

use App\Nami\HasNamiField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{

    use HasNamiField;

    public $fillable = ['is_try', 'is_member', 'name', 'is_filterable', 'nami_id'];
    public $timestamps = false;

    public $casts = [
        'nami_id' => 'integer'
    ];

    public function subactivities(): BelongsToMany {
        return $this->belongsToMany(Subactivity::class);
    }

}
