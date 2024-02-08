<?php

namespace App\Form\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
