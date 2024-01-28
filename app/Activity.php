<?php

namespace App;

use App\Nami\HasNamiField;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory;
    use HasNamiField;
    use Sluggable;

    public $fillable = ['is_try', 'has_efz', 'is_member', 'name', 'is_filterable', 'nami_id'];
    public $timestamps = false;

    public $casts = [
        'nami_id' => 'integer',
        'is_filterable' => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    /**
     * @return BelongsToMany<Subactivity>
     */
    public function subactivities(): BelongsToMany
    {
        return $this->belongsToMany(Subactivity::class);
    }
}
