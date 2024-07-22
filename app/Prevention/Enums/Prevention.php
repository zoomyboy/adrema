<?php

namespace App\Prevention\Enums;

use App\Member\Member;
use Carbon\Carbon;

enum Prevention
{
    case EFZ;
    case PS;
    case MOREPS;
    case VK;

    public function text(): string
    {
        return match ($this) {
            static::EFZ => 'erweitertes Führungszeugnis',
            static::PS => 'Präventionsschulung Basis Plus',
            static::MOREPS => 'Präventionsschulung (Auffrischung)',
            static::VK => 'Verhaltenskodex',
        };
    }

    public function tooltip(bool $value): string
    {
        return $this->text() . ' ' . ($value ? 'vorhanden' : 'nicht vorhanden');
    }

    public function letter(): string
    {
        return match ($this) {
            static::EFZ => 'F',
            static::PS => 'P',
            static::MOREPS => 'A',
            static::VK => 'V',
        };
    }

    /**
     * @param array<int, self> $preventions
     */
    public static function items(array $preventions)
    {
        return collect(static::cases())->map(fn ($case) => [
            'letter' => $case->letter(),
            'value' => !in_array($case, $preventions),
            'tooltip' => $case->tooltip(!in_array($case, $preventions)),
        ]);
    }
}
