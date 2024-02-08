<?php

namespace App\Form\Resources;

use App\Form\Fields\Field;
use App\Form\Models\Form;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $attributes = collect([]);

        foreach ($this->form->getFields() as $field) {
            $attributes = $attributes->merge(Field::fromConfig($field)->presentValue($this->data[$field['key']]));
        }

        return $attributes;
    }

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
