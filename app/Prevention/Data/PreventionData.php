<?php

namespace App\Prevention\Data;

use App\Prevention\Enums\Prevention;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class PreventionData extends Data
{
    public function __construct(public Prevention $type, public Carbon $expires) {}

    public function expiresAt(Carbon $date): bool
    {
        return $this->expires->isSameDay($date);
    }

    public function text(): string
    {
        return str($this->type->text())->when(
            !$this->expiresAt(now()),
            fn($str) => $str->append(' (fällig am ' . $this->expires->format('d.m.Y') . ')')
        );
    }
}
