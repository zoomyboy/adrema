<?php

namespace App\Nami\Api;

use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\MembershipEntry;

class MembershipsOfAction
{
    use AsAction;

    /**
     * @return Collection<int, MembershipEntry>
     */
    public function handle(Api $api, int $namiId): Collection
    {
        return $api->membershipsOf($namiId);
    }
}
