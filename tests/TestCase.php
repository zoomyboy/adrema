<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Zoomyboy\LaravelNami\Nami;
use Zoomyboy\LaravelNami\FakesNami;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use FakesNami;

    public function setUp(): void {
        parent::setUp();
    
        $this->fakeNami();
    }

}
