<?php

namespace App;

use App\Group\Enums\Level;
use App\Nami\HasNamiField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public static function booted(): void
    {
        static::creating(function (self $group) {
            if (!$group->getAttribute('inner_name') && $group->getAttribute('name')) {
                $group->setAttribute('inner_name', $group->getAttribute('name'));
            }
        });
    }
}
