<?php

namespace App\Form\Models;

use App\Form\Fields\Field;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
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
        'active_columns' => 'json',
        'description' => 'json',
    ];

    /** @var array<int, string> */
    public $dates = ['from', 'to', 'registration_from', 'registration_until'];

    /**
     * @return SluggableConfig
     */
    public function sluggable(): array
    {
        return [
            'slug' => ['source' => ['name']],
        ];
    }

    /**
     * @return HasMany<Participant>
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
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

    /**
     * @return array<string, mixed>
     */
    public function getRegistrationRules(): array
    {
        return $this->getFields()->reduce(function ($carry, $current) {
            $field = Field::fromConfig($current);

            return [
                ...$carry,
                ...$field->getRegistrationRules($this),
            ];
        }, []);
    }

    /**
     * @return array<string, mixed>
     */
    public function getRegistrationMessages(): array
    {
        return $this->getFields()->reduce(function ($carry, $current) {
            $field = Field::fromConfig($current);

            return [
                ...$carry,
                ...$field->getRegistrationMessages($this),
            ];
        }, []);
    }

    /**
     * @return array<string, mixed>
     */
    public function getRegistrationAttributes(): array
    {
        return $this->getFields()->reduce(function ($carry, $current) {
            $field = Field::fromConfig($current);

            return [
                ...$carry,
                ...$field->getRegistrationAttributes($this),
            ];
        }, []);
    }

    /**
     * @return Collection<string, mixed>
     */
    public function getFields(): Collection
    {
        return collect($this->config['sections'])->reduce(fn ($carry, $current) => $carry->merge($current['fields']), collect([]));
    }


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

    public static function booted(): void
    {
        static::saving(function (self $model) {
            if (is_null($model->active_columns)) {
                $model->setAttribute('active_columns', $model->getFields()->take(4)->pluck('key')->toArray());
            }
        });
    }
}
