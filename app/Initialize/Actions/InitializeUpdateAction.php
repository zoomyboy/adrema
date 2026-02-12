<?php

namespace App\Initialize\Actions;

use App\Initialize\InitializeGroups;
use App\Setting\NamiSettings;
use Lorisleiva\Actions\Concerns\AsAction;

class InitializeUpdateAction
{
    use AsAction;

    public string $commandSignature = 'initialize:update';

    /**
     * @var array<int, class-string>
     */
    public array $initializers = [
        InitializeGroups::class,
    ];

    public function handle(): void
    {
        $api = app(NamiSettings::class)->login();

        foreach ($this->initializers as $initializer) {
            app($initializer)->handle($api);
        }
    }
}
