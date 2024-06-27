<?php

namespace App\Fileshare\ConnectionTypes;

use Spatie\LaravelData\Data;

abstract class ConnectionType extends Data
{
    abstract public function check(): bool;

    /**
     * @return array<string, mixed>
     */
    abstract public static function defaults(): array;

    abstract public static function title(): string;

    /**
     * @return array<int, array{label: string, key: string, type: string}>
     */
    abstract public static function fields(): array;

    /**
     * @return array<int, mixed>
     */
    public static function forSelect(): array
    {
        return collect(glob(base_path('app/Fileshare/ConnectionTypes/*')))
            ->map(fn ($file) => 'App\\Fileshare\\ConnectionTypes\\' . pathinfo($file, PATHINFO_FILENAME))
            ->filter(fn ($file) => $file !== static::class)
            ->values()
            ->map(fn ($file) => ['id' => $file, 'name' => $file::title(), 'defaults' => $file::defaults(), 'fields' => $file::fields()])
            ->toArray();
    }
}
