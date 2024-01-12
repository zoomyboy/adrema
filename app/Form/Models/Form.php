<?php

namespace App\Form\Models;

use App\Form\FilterScope;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    use Sluggable;

    public $guarded = [];

    public $casts = [
        'config' => 'json',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => ['source' => ['name']],
        ];
    }

    /** @var array<int, string> */
    public $dates = ['from', 'to', 'registration_from', 'registration_until'];

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeWithFilter(Builder $query, FilterScope $filter): Builder
    {
        return $filter->apply($query);
    }
}
