<?php

namespace Tests\Feature\Form;

use Tests\TestCase;
use App\Form\Fields\CheckboxesField;
use App\Form\Fields\CheckboxField;
use App\Form\Fields\DateField;
use App\Form\Fields\DropdownField;
use App\Form\Fields\GroupField;
use App\Form\Fields\NamiField;
use App\Form\Fields\RadioField;
use App\Form\Fields\TextareaField;
use App\Form\Fields\TextField;
use Faker\Generator;

class FormTestCase extends TestCase
{
    protected function namiField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(NamiField::class)->key($key ?? $this->randomKey());
    }

    protected function textField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(TextField::class)->key($key ?? $this->randomKey());
    }

    protected function checkboxesField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(CheckboxesField::class)->key($key ?? $this->randomKey());
    }

    protected function textareaField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(TextareaField::class)->key($key ?? $this->randomKey());
    }

    protected function dropdownField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(DropdownField::class)->key($key ?? $this->randomKey());
    }

    protected function dateField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(DateField::class)->key($key ?? $this->randomKey());
    }

    protected function radioField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(RadioField::class)->key($key ?? $this->randomKey());
    }

    protected function checkboxField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(CheckboxField::class)->key($key ?? $this->randomKey());
    }

    protected function groupField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(GroupField::class)->key($key ?? $this->randomKey());
    }

    protected function randomKey(): string
    {
        return preg_replace('/[\-0-9]/', '', str()->uuid().str()->uuid());
    }
}
