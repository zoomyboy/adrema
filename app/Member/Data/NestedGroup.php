<?php

namespace App\Member\Data;

use App\Group;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class NestedGroup extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {
    }

    /**
     * @return Collection<int, array{name: string, id: int}>
     */
    public static function forSelect(?int $parentId = null, int $level = 0): Collection
    {
        $groups = collect([]);

        foreach (Group::where('parent_id', $parentId)->orderBy('name')->get()->toBase() as $group) {
            $groups->push(['name' => str_repeat('- ', $level).$group->name, 'id' => $group->id]);
            $groups = $groups->merge(static::forSelect($group->id, $level + 1));
        }

        return $groups;
    }

    /**
     * @return DataCollection<int, static>
     */
    public static function cacheForSelect(): DataCollection
    {
        return static::collection(static::forSelect());
    }
}
