<?php

namespace App\Form\Resources;

use App\Form\Fields\Field;
use App\Lib\HasMeta;
use Illuminate\Http\Resources\Json\JsonResource;

class FormtemplateResource extends JsonResource
{

    use HasMeta;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            ...parent::toArray($request),
            'links' => [
                'update' => route('formtemplate.update', ['formtemplate' => $this->getModel()]),
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(): array
    {
        return [
            'fields' => Field::asMeta(),
            [
                [
                    'id' => 'TextField',
                    'name' => 'Text',
                    'default' => [
                        'name' => '',
                        'type' => 'TextField',
                        'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 12],
                        'default' => '',
                        'required' => false,
                    ]
                ]
            ],
            'links' => [
                'store' => route('formtemplate.store'),
            ],
            'default' => [
                'name' => '',
                'config' => [
                    'sections' => [],
                ]
            ],
            'section_default' => [
                'name' => '',
                'intro' => '',
                'fields' => [],
            ]
        ];
    }
}
