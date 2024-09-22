<?php

namespace App\Mailgateway\Models;

use App\Mailgateway\Casts\TypeCast;
use Database\Factories\Mailgateway\Models\MailgatewayFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mailgateway extends Model
{
    /** @use HasFactory<MailgatewayFactory> */
    use HasFactory;
    use HasUuids;

    public $casts = ['type' => TypeCast::class];
    public $guarded = [];
}
