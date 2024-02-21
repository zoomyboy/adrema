<?php

namespace Tests\Feature\Form;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Lib\CreatesFormFields;

class FormTestCase extends TestCase
{
    use CreatesFormFields;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('temp');
    }
}
