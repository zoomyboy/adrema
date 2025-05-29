<?php

namespace App\Prevention\Enums;

use App\Prevention\Data\PreventionData;
use Illuminate\Support\Collection;

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
     * @param Collection<int, PreventionData> $preventions
     * @return Collection<int, array{letter: string, value: bool, tooltip: string}>
     */
    public static function items(Collection $preventions): Collection
    {
        return collect(static::cases())->map(fn($case) => [
            'letter' => $case->letter(),
            'value' => $preventions->pluck('type')->doesntContain($case),
            'tooltip' => $case->tooltip($preventions->pluck('type')->doesntContain($case)),
        ]);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return collect(static::cases())->map(fn($case) => [
            'id' => $case->name,
            'name' => $case->text(),
        ])->toArray();
    }
}
