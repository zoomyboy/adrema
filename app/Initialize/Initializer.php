<?php

namespace App\Initialize;

use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\NamiUser;

class Initializer {

    public static array $initializers = [
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

    public function run(NamiUser $namiUser): void {
        foreach (static::$initializers as $initializer) {
            (new $initializer($namiUser->api()))->handle($namiUser);
        }
    }

}
