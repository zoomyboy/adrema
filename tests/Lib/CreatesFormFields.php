<?php

namespace Tests\Lib;

use App\Form\Fields\CheckboxesField;
use App\Form\Fields\CheckboxField;
use App\Form\Fields\DateField;
use App\Form\Fields\DropdownField;
use App\Form\Fields\EmailField;
use App\Form\Fields\GroupField;
use App\Form\Fields\NamiField;
use App\Form\Fields\NumberField;
use App\Form\Fields\RadioField;
use App\Form\Fields\TextareaField;
use App\Form\Fields\TextField;
use Faker\Generator;
use Tests\Feature\Form\FormtemplateFieldRequest;

trait CreatesFormFields
{
    protected static function namiField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(NamiField::class)->key($key ?? static::randomKey());
    }

    protected static function textField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(TextField::class)->key($key ?? static::randomKey());
    }

    protected static function numberField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(NumberField::class)->key($key ?? static::randomKey());
    }

    protected static function emailField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(EmailField::class)->key($key ?? static::randomKey());
    }

    protected static function checkboxesField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(CheckboxesField::class)->key($key ?? static::randomKey());
    }

    protected static function textareaField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(TextareaField::class)->key($key ?? static::randomKey());
    }

    protected static function dropdownField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(DropdownField::class)->key($key ?? static::randomKey());
    }

    protected static function dateField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(DateField::class)->key($key ?? static::randomKey());
    }

    protected static function radioField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(RadioField::class)->key($key ?? static::randomKey());
    }

    protected static function checkboxField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(CheckboxField::class)->key($key ?? static::randomKey());
    }

    protected static function groupField(?string $key = null): FormtemplateFieldRequest
    {
        return FormtemplateFieldRequest::type(GroupField::class)->key($key ?? static::randomKey());
    }

    protected static function randomKey(): string
    {
        return preg_replace('/[\-0-9]/', '', str()->uuid() . str()->uuid());
    }
}
