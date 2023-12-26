<?php

namespace App\Form\Fields;

use Illuminate\Support\Collection;

abstract class Field
{

    abstract public static function name(): string;
    abstract public static function meta(): array;
    abstract public static function default();

    public static function asMeta(): array
    {
        return self::classNames()->map(fn ($class) => $class::allMeta())->toArray();
    }


    /**
     * @return Collection<int, class-string<self>>
     */
    private static function classNames(): Collection
    {
        return collect(glob(base_path('app/Form/Fields/*.php')))
            ->filter(fn ($fieldClass) => preg_match('/[A-Za-z]Field\.php$/', $fieldClass) === 1)
            ->map(fn ($fieldClass) => str($fieldClass)->replace(base_path(''), '')->replace('/app', '/App')->replace('.php', '')->replace('/', '\\')->toString())
            ->values();
    }

    public static function allMeta(): array
    {
        return [
            'id' => class_basename(static::class),
            'name' => static::name(),
            'default' => [
                'name' => '',
                'type' => class_basename(static::class),
                'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
                'default' => static::default(),
                'required' => false,
                ...static::meta(),
            ],
        ];
    }
}
