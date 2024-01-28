<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class EndToEndTestCase extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->useMeilisearch();
    }
}
