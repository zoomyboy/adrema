<?php

namespace App\Initialize;

use App\Setting\NamiSettings;
use Zoomyboy\LaravelNami\Api;

class Initializer {

    public NamiSettings $settings;
    public array $initializers = [
        InitializeNationalities::class,
        InitializeFees::class,
        InitializeConfessions::class,
        InitializeCountries::class,
        InitializeGenders::class,
        InitializeRegions::class,
        InitializeActivities::class,
        InitializeCourses::class,
        InitializeMembers::class,
    ];

    public function __construct(NamiSettings $settings)
    {
        $this->settings = $settings;
    }

    public function run(): void {
        foreach ($this->initializers as $initializer) {
            app($initializer)->handle($this->settings->login());
        }
    }

}
