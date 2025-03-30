<?php

namespace App\Maildispatcher\Models;

use App\Mailgateway\Models\Mailgateway;
use Database\Factories\Maildispatcher\Models\MaildispatcherFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maildispatcher extends Model
{
    /** @use HasFactory<MaildispatcherFactory> */
    use HasFactory;
    use HasUuids;

    public $guarded = [];
    public $timestamps = false;

    public $casts = [
        'filter' => 'json',
    ];

    /**
     * @return BelongsTo<Mailgateway, $this>
     */
    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Mailgateway::class);
    }
}
