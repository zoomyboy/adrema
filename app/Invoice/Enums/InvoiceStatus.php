<?php

namespace App\Invoice\Enums;

use Illuminate\Support\Collection;

enum InvoiceStatus: string
{
    case NEW = 'Neu';
    case SENT = 'Rechnung gestellt';
    case PAID = 'Rechnung beglichen';

    /**
     * @return Collection<int, string>
     */
    public static function values(): Collection
    {
        return collect(static::cases())->map(fn ($case) => $case->value);
    }

    /**
     * @return array<int, array{id: string, name: string}>
     */
    public static function forSelect(): array
    {
        return array_map(fn ($case) => ['id' => $case->value, 'name' => $case->value], static::cases());
    }
}
