<?php

namespace App\Course\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $fillable = ['name', 'nami_id'];

    public function getShortNameAttribute(): string
    {
        return str($this->name)
            ->trim()
            ->replaceFirst('Baustein', '')
            ->trim()
            ->replaceMatches('/ - .*/', '');
    }
}
