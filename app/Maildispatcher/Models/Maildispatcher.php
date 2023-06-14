<?php

namespace App\Maildispatcher\Models;

use App\Mailgateway\Models\Mailgateway;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maildispatcher extends Model
{
    use HasFactory;
    use HasUuids;

    public $guarded = [];
    public $timestamps = false;

    public $casts = [
        'filter' => 'json',
    ];

    public static function booted(): void
    {
        static::deleting(function ($dispatcher) {
            foreach ($dispatcher->gateway->type->list($dispatcher->name, $dispatcher->gateway->domain) as $email) {
                $dispatcher->gateway->type->remove($dispatcher->name, $dispatcher->gateway->domain, $email->email);
            }
        });
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Mailgateway::class);
    }
}
