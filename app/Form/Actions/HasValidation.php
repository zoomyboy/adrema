<?php

namespace App\Form\Actions;

use Illuminate\Validation\Rule;
use App\Form\Fields\Field;
use Illuminate\Validation\Validator;
use Lorisleiva\Actions\ActionRequest;

trait HasValidation
{
    /**
     * @return array<string, mixed>
     */
    public function globalRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'config' => 'array',
            'config.sections.*.name' => 'required',
            'config.sections.*.intro' => 'nullable|string',
            'config.sections.*.fields' => 'array',
            'config.sections.*.fields.*.name' => 'required|string',
            'config.sections.*.fields.*.type' => ['required', 'string', Rule::in(array_column(Field::asMeta(), 'id'))],
            'config.sections.*.fields.*.key' => ['required', 'string', 'regex:/^[a-zA-Z_]*$/'],
            'config.sections.*.fields.*.columns' => 'required|array',
            'config.sections.*.fields.*.*' => '',
            'config.sections.*.fields.*.columns.mobile' => 'required|numeric|gt:0|lte:2',
            'config.sections.*.fields.*.columns.tablet' => 'required|numeric|gt:0|lte:4',
            'config.sections.*.fields.*.columns.desktop' => 'required|numeric|gt:0|lte:6',
            'mail_top' => 'array',
            'mail_bottom' => 'array',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function globalValidationAttributes(): array
    {
        return [
            'config.sections.*.name' => 'Sektionsname',
            'config.sections.*.fields.*.name' => 'Feldname',
            'config.sections.*.fields.*.type' => 'Feldtyp',
            'config.sections.*.fields.*.key' => 'Feldkey',
        ];
    }

    public function withValidator(Validator $validator, ActionRequest $request): void
    {
        if (!$validator->passes()) {
            return;
        }

        foreach ($request->input('config.sections') as $sindex => $section) {
            foreach (data_get($section, 'fields') as $findex => $field) {
                $fieldClass = Field::classFromType($field['type']);
                if (!$fieldClass) {
                    continue;
                }
                foreach ($fieldClass::metaRules() as $fieldName => $rules) {
                    $validator->addRules(["config.sections.{$sindex}.fields.{$findex}.{$fieldName}" => $rules]);
                }
                foreach ($fieldClass::metaAttributes() as $fieldName => $attribute) {
                    $validator->addCustomAttributes(["config.sections.{$sindex}.fields.{$findex}.{$fieldName}" => $attribute]);
                }
            }
        }
    }
}
