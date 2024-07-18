<?php

namespace App\Prevention\Enums;

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
}
