<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;
    public $timestamps = false;

    public $fillable = ['name', 'nami_id', 'is_null'];

    public $casts = [
        'is_null' => 'boolean',
    ];

    public static function forSelect(): Collection
    {
        return static::select('id', 'name')->get();
    }
}
