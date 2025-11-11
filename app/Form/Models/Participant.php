<?php

namespace App\Form\Models;

use App\Form\Data\FieldCollection;
use App\Form\Data\FormConfigData;
use App\Form\Editor\FormConditionResolver;
use App\Form\Mails\ConfirmRegistrationMail;
use App\Lib\Editor\Condition;
use App\Member\Member;
use App\Prevention\Contracts\Preventable;
use Database\Factories\Form\Models\ParticipantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Laravel\Scout\Searchable;
use stdClass;

class Participant extends Model implements Preventable
{

    /** @use HasFactory<ParticipantFactory> */
    use HasFactory;
    use Searchable;

    public $guarded = [];

    public $casts = [
        'data' => 'json',
        'last_remembered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Form, $this>
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * @return HasMany<Participant, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return BelongsTo<Member, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
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

        Mail::to($this->getMailRecipient())->queue(new ConfirmRegistrationMail($this));
    }

    /**
     * @inheritdoc
     */
    public function preventions(): Collection
    {
        return $this->member?->preventions($this->form->from) ?: collect([]);
    }

    public function getMailRecipient(): ?stdClass
    {
        return $this->getFields()->getMailRecipient();
    }

    public function preventableSubject(): string
    {
        return 'Nachweise erforderlich für deine Anmeldung zu ' . $this->form->name;
    }

    public function searchableAs(): string
    {
        return $this->form->participantsSearchableAs();
    }

    /** @return array<string, mixed> */
    public function toSearchableArray(): array
    {
        return [...$this->data, 'parent-id' => $this->parent_id, 'created_at' => $this->created_at->timestamp];
    }

    public function matchesCondition(Condition $condition): bool {
        return app(FormConditionResolver::class)->forParticipant($this)->filterCondition($condition);
    }
}
