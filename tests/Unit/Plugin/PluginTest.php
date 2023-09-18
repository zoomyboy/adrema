<?php

namespace Tests\Unit\Plugin;

use Tests\TestCase;

class PluginTest extends TestCase
{

    public function setUp(): void
    {}

    public function testItCanRegisterAPlugin(): void
    {
        $pluginsPath = __DIR__.'/../../../plugins/Test';
        @mkdir($pluginsPath);

        file_put_contents($pluginsPath.'/ServiceProvider.php', '<?php
            namespace Plugins\Test;
            use Illuminate\Support\ServiceProvider as BaseServiceProvider;
            class ServiceProvider extends BaseServiceProvider
            {
                public function register() {}
                public function boot() {}
            }');


        parent::setUp();

        $this->assertInstanceOf('Plugins\\Test\\ServiceProvider', app()->getProvider('Plugins\\Test\\ServiceProvider'));

        array_map(fn ($file) => unlink($file), glob($pluginsPath.'/*'));
        rmdir($pluginsPath);
    }
}
