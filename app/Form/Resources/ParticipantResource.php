<?php

namespace App\Form\Resources;

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
        return $this->form->getFields()->mapWithKeys(function ($field) {
            return [$field['key'] => $this->data[$field['key']]];
        })->toArray();
    }

    public static function meta(Form $form): array
    {
        return [
            'columns' => $form->getFields()->map(fn ($field) => [
                'name' => $field['name'],
                'base_type' => class_basename($field['type']),
                'id' => $field['key'],
            ])
        ];
    }
}
