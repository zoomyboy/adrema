<?php

namespace App;

use Database\Factories\RegionFactory;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    /** @use HasFactory<RegionFactory> */
    use HasFactory;
    public $timestamps = false;

    public $fillable = ['name', 'nami_id', 'is_null'];

    public $casts = [
        'is_null' => 'boolean',
    ];

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public static function forSelect(): Collection
    {
        return static::select('id', 'name')->get();
    }
}
