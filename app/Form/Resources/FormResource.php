<?php

namespace App\Form\Resources;

use App\Form\Enums\NamiType;
use App\Form\Enums\SpecialType;
use App\Form\Fields\Field;
use App\Form\Scopes\FormFilterScope;
use App\Form\Models\Form;
use App\Form\Models\Formtemplate;
use App\Group;
use App\Lib\HasMeta;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Form
 */
class FormResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'from_human' => $this->from->format('d.m.Y'),
            'to_human' => $this->to->format('d.m.Y'),
            'from' => $this->from->format('Y-m-d'),
            'to' => $this->to->format('Y-m-d'),
            'excerpt' => $this->excerpt,
            'description' => $this->description,
            'mail_top' => $this->mail_top,
            'mail_bottom' => $this->mail_bottom,
            'registration_from' => $this->registration_from?->format('Y-m-d H:i:s'),
            'registration_until' => $this->registration_until?->format('Y-m-d H:i:s'),
            'config' => $this->config,
            'participants_count' => $this->participants_count,
            'links' => [
                'participant_index' => route('form.participant.index', ['form' => $this->getModel()]),
                'update' => route('form.update', ['form' => $this->getModel()]),
                'destroy' => route('form.destroy', ['form' => $this->getModel()]),
                'is_dirty' => route('form.is-dirty', ['form' => $this->getModel()]),
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
            'filter' => FormFilterScope::fromRequest(request()->input('filter', '')),
            'links' => [
                'store' => route('form.store'),
                'formtemplate_index' => route('formtemplate.index'),
            ],
            'templates' => FormtemplateResource::collection(Formtemplate::get()),
            'namiTypes' => NamiType::forSelect(),
            'specialTypes' => SpecialType::forSelect(),
            'default' => [
                'description' => [],
                'name' => '',
                'excerpt' => '',
                'from' => null,
                'to' => null,
                'registration_from' => null,
                'registration_until' => null,
                'mail_top' => null,
                'mail_bottom' => null,
                'config' => null,
                'header_image' => null,
                'mailattachments' => [],
                'id' => null,
            ],
            'section_default' => [
                'name' => '',
                'intro' => '',
                'fields' => [],
            ]
        ];
    }
}
