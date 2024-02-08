<?php

namespace App;

use App\Group\Enums\Level;
use App\Nami\HasNamiField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;
    use HasNamiField;

    public $fillable = ['nami_id', 'name', 'inner_name', 'level', 'parent_id'];
    public $timestamps = false;

    public $casts = [
        'level' => Level::class
    ];

    /**
     * @return BelongsTo<static, self>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * @return HasMany<self>
     */
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public static function booted(): void
    {
        static::creating(function (self $group) {
            if (!$group->getAttribute('inner_name') && $group->getAttribute('name')) {
                $group->setAttribute('inner_name', $group->getAttribute('name'));
            }
        });
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    public static function forSelect(?self $parent = null, string $prefix = ''): array
    {
        $result = self::where('parent_id', $parent ? $parent->id : null)->withCount('children')->get();

        return $result
            ->reduce(
                fn ($before, $group) => $before->concat([['id' => $group->id, 'name' => $prefix . ($group->display())]])
                    ->concat($group->children_count > 0 ? self::forSelect($group, $prefix . '-- ') : []),
                collect([])
            )
            ->toArray();
    }

    public function display(): string
    {
        return $this->inner_name ?: $this->name;
    }
}
