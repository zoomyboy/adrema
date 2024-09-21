<?php

namespace Tests;

use App\Form\Models\Form;
use App\Member\Member;
use Laravel\Scout\Console\FlushCommand;
use Laravel\Scout\Console\SyncIndexSettingsCommand;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;

abstract class EndToEndTestCase extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useMeilisearch();
    }


    public function useMeilisearch(): self
    {
        config()->set('scout.driver', 'meilisearch');
        Artisan::call(FlushCommand::class, ['model' => Member::class]);
        Artisan::call(FlushCommand::class, ['model' => Form::class]);
        Artisan::call(SyncIndexSettingsCommand::class);

        return $this;
    }
}
