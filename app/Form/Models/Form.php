<?php

namespace App\Form\Models;

use App\Form\FilterScope;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Zoomyboy\MedialibraryHelper\DefersUploads;

class Form extends Model implements HasMedia
{
    use HasFactory;
    use Sluggable;
    use InteractsWithMedia;
    use DefersUploads;

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('headerImage')
            ->singleFile()
            ->maxWidth(fn () => 500)
            ->forceFileName(fn (Form $model, string $name) => $model->slug);
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
