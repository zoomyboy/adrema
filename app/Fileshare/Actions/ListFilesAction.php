<?php

namespace App\Fileshare\Actions;

use App\Fileshare\Data\ResourceData;
use App\Fileshare\Models\Fileshare;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\DataCollection;

class ListFilesAction
{
    use AsAction;

    /**
     * @return DataCollection<int, ResourceData>
     */
    public function handle(ActionRequest $request, Fileshare $fileshare): DataCollection
    {
        return ResourceData::collection($fileshare->type->getSubDirectories($request->input('parent')))->wrap('data');
    }
}
