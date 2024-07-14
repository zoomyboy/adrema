<?php

namespace App\Form\Resources;

use App\Form\Enums\NamiType;
use App\Form\Enums\SpecialType;
use App\Form\Fields\Field;
use App\Form\Models\Formtemplate;
use App\Group;
use App\Lib\Editor\EditorData;
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
                'destroy' => route('formtemplate.destroy', ['formtemplate' => $this->getModel()]),
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
            'namiTypes' => NamiType::forSelect(),
            'specialTypes' => SpecialType::forSelect(),
            'links' => [
                'store' => route('formtemplate.store'),
                'form_index' => route('form.index'),
            ],
            'default' => [
                'name' => '',
                'mail_top' => EditorData::default(),
                'mail_bottom' => EditorData::default(),
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
