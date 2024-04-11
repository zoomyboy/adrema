<?php

namespace App\Form\Resources;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_display' => $this->created_at->format('d.m.Y'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(Form $form): array
    {
        return [
            'form_meta' => $form->meta,
            'links' => [
                'update_form_meta' => route('form.update-meta', ['form' => $form]),
            ],
            'columns' => $form->getFields()
                ->map(fn ($field) => [
                    'name' => $field->name,
                    'base_type' => class_basename($field),
                    'id' => $field->key,
                    'display_attribute' => $field->getDisplayAttribute(),
                ])
                ->push([
                    'name' => 'Registriert am',
                    'id' => 'created_at',
                    'display_attribute' => 'created_at_display'
                ])
        ];
    }
}
