<?php

namespace App\Member\Actions;

use App\Member\Member;
use App\Member\MemberResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SearchAction
{
    use AsAction;

    /**
     * @return Collection<int, Member>
     */
    public function handle(string $search): Collection
    {
        return Member::search($search)->query(fn ($query) => $query->ordered())->get();
    }

    public function asController(ActionRequest $request): AnonymousResourceCollection
    {
        if (null !== $request->input('minLength') && strlen($request->input('search', '')) < $request->input('minLength')) {
            return MemberResource::collection($this->empty());
        }

        return MemberResource::collection($this->handle($request->input('search', '')));
    }

    /**
     * @return Collection<int, Member>
     */
    private function empty(): Collection
    {
        return Member::where('id', -1)->get();
    }
}
