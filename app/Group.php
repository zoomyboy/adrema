<?php

namespace App;

use App\Nami\HasNamiField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model
{
    use HasFactory;
    use HasNamiField;

    public $fillable = ['nami_id', 'name', 'parent_id'];
    public $timestamps = false;

    /**
     * @return BelongsTo<static, self>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }
}
