<?php

namespace App\Form\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Zoomyboy\MedialibraryHelper\DefersUploads;

class Form extends Model implements HasMedia
{
    use HasFactory;
    use Sluggable;
    use InteractsWithMedia;
    use DefersUploads;
    use Searchable;

    public $guarded = [];

    public $casts = [
        'config' => 'json',
    ];

    /**
     * @return SluggableConfig
     */
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
            ->forceFileName(fn (Form $model, string $name) => $model->slug)
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('square')->fit(Manipulations::FIT_CROP, 400, 400);
            });
    }

    /** @var array<int, string> */
    public $dates = ['from', 'to', 'registration_from', 'registration_until'];

    // --------------------------------- Searching ---------------------------------
    // *****************************************************************************

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return [
            'from' => $this->from->timestamp,
            'to' => $this->to->timestamp,
            'name' => $this->name,
        ];
    }
}
