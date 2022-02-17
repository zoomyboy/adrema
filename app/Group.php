<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model
{
    use HasFactory;

    public $fillable = ['nami_id', 'name', 'parent_id'];
    public $timestamps = false;

    public static function nami(int $id): self
    {
        return static::firstWhere('nami_id', $id);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }
}
