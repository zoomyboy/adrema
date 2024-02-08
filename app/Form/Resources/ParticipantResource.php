<?php

namespace App\Form\Resources;

use App\Form\Fields\Field;
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
        $attributes = collect([]);

        foreach ($this->form->getFields() as $field) {
            $attributes = $attributes->merge(Field::fromConfig($field)->presentValue($this->data[$field['key']]));
        }

        return $attributes->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(Form $form): array
    {
        return [
            'active_columns' => $form->active_columns,
            'columns' => $form->getFields()->map(function ($field) {
                $field = Field::fromConfig($field);
                return [
                    'name' => $field->name,
                    'base_type' => class_basename($field),
                    'id' => $field->key,
                    'display_attribute' => $field->displayAttribute(),
                ];
            })
        ];
    }
}
