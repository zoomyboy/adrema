<?php

namespace Tests\Unit\Plugin;

use Tests\TestCase;
use Plugins\Test\ServiceProvider;

class PluginTest extends TestCase
{

    public function setUp(): void
    {}

    public function testItCanRegisterAPlugin(): void
    {
        $pluginsPath = __DIR__.'/../../../plugins/Test';
        @mkdir($pluginsPath, 0755, true);

        file_put_contents($pluginsPath.'/ServiceProvider.php', '<?php
            namespace Plugins\Test;
            use Illuminate\Support\ServiceProvider as BaseServiceProvider;
            class ServiceProvider extends BaseServiceProvider
            {
                public function register() {}
                public function boot() {}
            }');


        parent::setUp();

        $this->assertInstanceOf(ServiceProvider::class, app()->getProvider('Plugins\\Test\\ServiceProvider'));

        array_map(fn ($file) => unlink($file), glob($pluginsPath.'/*'));
        rmdir($pluginsPath);
        rmdir(dirname($pluginsPath));
    }
}
