<?php

namespace App\Form\Resources;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Scopes\ParticipantFilterScope;
use App\Prevention\Enums\Prevention;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @mixin Participant
 */
class ParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            ...$this->getModel()->getFields()->present(),
            'id' => $this->id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_display' => $this->created_at->format('d.m.Y'),
            'children_count' => $this->children_count,
            'member_id' => $this->member_id,
            'prevention_items' => $this->member ? Prevention::items($this->member->preventions()) : [],
            'links' => [
                'assign' => route('participant.assign', ['participant' => $this->getModel()]),
                'destroy' => route('participant.destroy', ['participant' => $this->getModel()]),
                'children' => route('form.participant.index', ['form' => $this->form, 'parent' => $this->id]),
                'fields' => route('participant.fields', ['participant' => $this->getModel()]),
                'update' => route('participant.update', ['participant' => $this->getModel()]),
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(Form $form): array
    {
        /** @var Collection<int, array<string, mixed>> */
        $fieldData = $form->getFields()
            ->map(fn ($field) => [
                'name' => $field->name,
                'base_type' => class_basename($field),
                'id' => $field->key,
                'display_attribute' => $field->getDisplayAttribute(),
            ]);

        $filterData = $form->getFields()
            ->map(fn ($field) => [
                ...$field->toArray(),
                'base_type' => class_basename($field),
            ]);

        return [
            'filter' => ParticipantFilterScope::fromRequest(request()->input('filter', ''))->setForm($form),
            'form_config' => $form->config,
            'default_filter_value' => ParticipantFilterScope::$nan,
            'filters' => $filterData,
            'form_meta' => $form->meta,
            'has_nami_field' => $form->getFields()->hasNamiField(),
            'links' => [
                'update_form_meta' => route('form.update-meta', ['form' => $form]),
                'store_participant' => route('form.participant.store', ['form' => $form]),
            ],
            'columns' => $fieldData->push([
                'name' => 'Registriert am',
                'id' => 'created_at',
                'display_attribute' => 'created_at_display'
            ])->push([
                'name' => 'Prävention',
                'id' => 'prevention',
                'display_attribute' => 'prevention_display'
            ]),
        ];
    }
}
