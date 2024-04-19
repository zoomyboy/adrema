<?php

namespace App\Form\Models;

use App\Form\Data\FieldCollection;
use App\Form\Data\FormConfigData;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'config' => FormConfigData::class,
        'meta' => 'json',
        'description' => 'json',
        'mail_top' => 'json',
        'mail_bottom' => 'json',
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
        $this->addMediaCollection('mailattachments')
            ->withDefaultProperties(fn () => [
                'conditions' => [
                    'mode' => 'all',
                    'ifs' => []
                ],
            ])
            ->withPropertyValidation(fn () => [
                'conditions.mode' => 'required|string|in:all,any',
                'conditions.ifs' => 'array',
                'conditions.ifs.*.field' => 'required',
                'conditions.ifs.*.comparator' => 'required',
                'conditions.ifs.*.value' => 'present',
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getRegistrationRules(): array
    {
        return $this->getFields()->reduce(fn ($carry, $field) => [
            ...$carry,
            ...$field->getRegistrationRules($this),
        ], []);
    }

    /**
     * @return array<string, mixed>
     */
    public function getRegistrationMessages(): array
    {
        return $this->getFields()->reduce(fn ($carry, $field) => [
            ...$carry,
            ...$field->getRegistrationMessages($this),
        ], []);
    }

    /**
     * @return array<string, mixed>
     */
    public function getRegistrationAttributes(): array
    {
        return $this->getFields()->reduce(fn ($carry, $field) => [
            ...$carry,
            ...$field->getRegistrationAttributes($this),
        ], []);
    }

    public function getFields(): FieldCollection
    {
        return $this->config->fields();
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
            if (is_null($model->meta)) {
                $model->setAttribute('meta', [
                    'active_columns' => $model->getFields()->count() ? $model->getFields()->take(4)->pluck('key')->toArray() : null,
                    'sorting' => $model->getFields()->count() ? [$model->getFields()->first()->key, 'asc'] : null,
                ]);
            }

            if (is_array(data_get($model->meta, 'active_columns'))) {
                $model->setAttribute('meta', [
                    ...$model->meta,
                    'active_columns' => array_values(array_intersect([...$model->getFields()->pluck('key')->toArray(), 'created_at'], $model->meta['active_columns'])),
                ]);
            }
        });
    }
}
