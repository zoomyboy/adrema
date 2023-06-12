<?php

namespace App\Maildispatcher\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Localmaildispatcher extends Model
{
    use HasFactory;
    use HasUuids;

    public $guarded = [];

    public $timestamps = false;

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(Maildispatcher::class);
    }
}
