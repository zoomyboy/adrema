<?php

namespace App\Nami\Api;

use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\Course;

class CoursesOfAction
{
    use AsAction;

    /**
     * @return Collection<int, Course>
     */
    public function handle(Api $api, int $namiId): Collection
    {
        return $api->coursesOf($namiId);
    }
}
