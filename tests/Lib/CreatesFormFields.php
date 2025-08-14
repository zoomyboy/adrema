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
use App\Form\FormSettings;
use App\Form\Models\Form;
use App\Member\Member;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;
use Tests\Feature\Form\FormtemplateFieldRequest;

trait CreatesFormFields
{


    /**
     * @param array<string, mixed> $attributes
     */
    public function createMember(array $attributes, ?callable $factoryCallback = null): Member
    {
        return call_user_func($factoryCallback ?: fn ($factory) => $factory, Member::factory()->defaults())
            ->create($attributes);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function register(Form $form, array $payload): TestResponse
    {
        return $this->postJson(route('form.register', ['form' => $form]), $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function registerLater(Form $form, array $payload, string $laterId): TestResponse
    {
        return $this->postJson(URL::signedRoute('form.register', ['form' => $form, 'later' => '1', 'id' => $laterId]), $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function registerLaterWithWrongSignature(Form $form, array $payload, string $laterId): TestResponse
    {
        return $this->postJson(route('form.register', ['form' => $form, 'later' => '1', 'id' => $laterId, 'signature' => '-1']), $payload);
    }

    public function setUpForm() {
        app(FormSettings::class)->fill(['clearCacheUrl' => 'http://event.com/clear-cache'])->save();

        Http::fake(function ($request) {
            if ($request->url() === 'http://event.com/clear-cache') {
                return Http::response('', 200);
            }
        });

        Storage::fake('temp');
    }

    protected function assertFrontendCacheCleared(): void
    {
        Http::assertSent(fn ($request) => $request->url() === 'http://event.com/clear-cache'
            && $request->method() === 'GET'
        );
    }

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
