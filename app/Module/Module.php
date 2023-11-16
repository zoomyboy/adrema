<?php

namespace App\Module;

enum Module: string
{

    case BILL = 'bill';
    case COURSE = 'course';
    case EVENT = 'event';

    public function title(): string
    {
        return match ($this) {
            static::BILL => 'Zahlungs-Management',
            static::COURSE => 'Ausbildung',
            static::EVENT => 'Veranstaltungen',
        };
    }

    /**
     * @return array<int, array{id: string, name: string}>
     */
    public static function forSelect(): array
    {
        return array_map(fn ($module) => ['id' => $module->value, 'name' => $module->title()], static::cases());
    }


    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn ($module) => $module->value, static::cases());
    }
}
