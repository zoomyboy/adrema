<?php

namespace App\Form\Models;

use App\Form\Data\FieldCollection;
use App\Form\Data\FormConfigData;
use App\Form\Mails\ConfirmRegistrationMail;
use App\Form\Scopes\ParticipantFilterScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;

class Participant extends Model
{
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'data' => 'json',
    ];

    /**
     * @return BelongsTo<Form, self>
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * @return HasMany<self>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @param  Builder<self> $query
     * @return Builder<self>
     */
    public function scopeWithFilter(Builder $query, ParticipantFilterScope $filter): Builder
    {
        return $filter->apply($query);
    }

    public function getFields(): FieldCollection
    {
        return FieldCollection::fromRequest($this->form, $this->data);
    }

    public function getConfig(): FormConfigData
    {
        return tap($this->form->config, function ($config) {
            $config->sections->each(function ($section) {
                $section->fields->each(function ($field) {
                    $field->value = $this->getFields()->find($field)->value;
                });
            });
        });
    }

    public function sendConfirmationMail(): void
    {
        if (!$this->getFields()->getMailRecipient()) {
            return;
        }

        Mail::to($this->getFields()->getMailRecipient())->queue(new ConfirmRegistrationMail($this));
    }
}
