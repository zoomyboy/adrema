<?php

namespace App\Form\Resources;

use App\Form\Enums\NamiField;
use App\Form\Fields\Field;
use App\Form\Models\Formtemplate;
use App\Group;
use App\Group\Enums\Level;
use App\Lib\HasMeta;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Formtemplate
 */
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
            'base_url' => url(''),
            'groups' => Group::forSelect(),
            'fields' => Field::asMeta(),
            'namiFields' => NamiField::forSelect(),
            'links' => [
                'store' => route('formtemplate.store'),
                'form_index' => route('form.index'),
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
