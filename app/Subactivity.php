<?php

namespace App;

use App\Nami\HasNamiField;
use Cviebrock\EloquentSluggable\Sluggable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subactivity extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasNamiField;
    use Sluggable;

    public $fillable = ['is_age_group', 'is_filterable', 'slug', 'name', 'nami_id'];

    /**
     * @var bool
     */
    public $timestamps = false;

    public $casts = [
        'is_age_group' => 'boolean',
        'is_filterable' => 'boolean',
    ];

    /**
     * @return array<string, array{source: string}>
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    /**
     * @return BelongsToMany<Activity, $this>
     */
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class);
    }
}
