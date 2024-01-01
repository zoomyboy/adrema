<?php

namespace App\Form\Resources;

use App\Form\Models\Form;
use App\Form\Models\Formtemplate;
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
            'name' => $this->name,
            'from_human' => $this->from?->format('d.m.Y'),
            'to_human' => $this->to?->format('d.m.Y'),
            'from' => $this->from?->format('Y-m-d'),
            'to' => $this->to?->format('Y-m-d'),
            'excerpt' => $this->excerpt,
            'description' => $this->description,
            'mail_top' => $this->mail_top,
            'mail_bottom' => $this->mail_bottom,
            'registration_from' => $this->registration_from?->format('Y-m-d H:i:s'),
            'registration_until' => $this->registration_until?->format('Y-m-d H:i:s'),
            'config' => $this->config,
        ];
    }

    public static function meta(): array
    {
        return [
            'links' => [
                'store' => route('form.store'),
            ],
            'templates' => FormtemplateResource::collection(Formtemplate::get()),
        ];
    }
}
