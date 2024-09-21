<?php

namespace Tests\Feature\Form;

use App\Form\FormSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Lib\CreatesFormFields;

class FormTestCase extends TestCase
{
    use CreatesFormFields;

    private string $clearCacheUrl = 'http://event.com/clear-cache';

    protected function setUp(): void
    {
        parent::setUp();

        app(FormSettings::class)->fill(['clearCacheUrl' => 'http://event.com/clear-cache'])->save();

        Http::fake(function ($request) {
            if ($request->url() === $this->clearCacheUrl) {
                return Http::response('', 200);
            }
        });

        Storage::fake('temp');
    }

    protected function assertFrontendCacheCleared(): void
    {
        Http::assertSent(fn ($request) => $request->url() === $this->clearCacheUrl && $request->method() === 'GET');
    }
}
