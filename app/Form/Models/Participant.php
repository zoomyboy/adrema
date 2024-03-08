<?php

namespace App\Form\Models;

use App\Form\Scopes\ParticipantFilterScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'data' => 'json',
    ];

    /**
     * @return BelongsTo<Form, self>
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * @return HasMany<self>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @param  Builder<self> $query
     * @return Builder<self>
     */
    public function scopeWithFilter(Builder $query, ParticipantFilterScope $filter): Builder
    {
        return $filter->apply($query);
    }
}
