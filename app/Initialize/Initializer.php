<?php

namespace App\Initialize;

use App\Setting\NamiSettings;

class Initializer
{
    public NamiSettings $settings;

    /**
     * @var array<int, class-string>
     */
    public array $initializers = [
        InitializeGroups::class,
        InitializeNationalities::class,
        InitializeFees::class,
        InitializeConfessions::class,
        InitializeCountries::class,
        InitializeGenders::class,
        InitializeRegions::class,
        InitializeCourses::class,
        InitializeMembers::class,
    ];

    public function __construct(NamiSettings $settings)
    {
        $this->settings = $settings;
    }

    public function run(): void
    {
        foreach ($this->initializers as $initializer) {
            app($initializer)->handle($this->settings->login());
        }
    }

    public function restore(): void
    {
        foreach (array_reverse($this->initializers) as $initializer) {
            app($initializer)->restore();
        }

        $settings = app(NamiSettings::class);
        $settings->mglnr = 0;
        $settings->password = '';
        $settings->default_group_id = 0;
        $settings->save();
    }
}
