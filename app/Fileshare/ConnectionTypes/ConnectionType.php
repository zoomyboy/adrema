<?php

namespace App\Fileshare\ConnectionTypes;

use App\Fileshare\Data\ResourceData;
use Illuminate\Filesystem\FilesystemAdapter;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

abstract class ConnectionType extends Data
{
    abstract public function check(): bool;

    /**
     * @return array<string, mixed>
     */
    abstract public static function defaults(): array;

    abstract public static function title(): string;

    abstract public function getFilesystem(): FilesystemAdapter;

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

    /**
     * @return array<int, string>
     */
    public function getSubDirectories(?string $parent): array
    {
        $filesystem = $this->getFilesystem();

        return $filesystem->directories($parent ?: '/');
    }
}
