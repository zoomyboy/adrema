<?php

namespace App\Form\Fields;

use Faker\Generator;
use Illuminate\Support\Collection;

abstract class Field
{

    abstract public static function name(): string;

    /** @return array<int, array{key: string, default: mixed, label: string, rules: array<string, mixed>}> */
    abstract public static function meta(): array;

    /** @return mixed */
    abstract public static function default();

    /** @return array<string, mixed> */
    abstract public static function fake(Generator $faker): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function asMeta(): array
    {
        return array_map(fn ($class) => $class::allMeta(), self::classNames());
    }

    /**
     * @return array<int, class-string<self>>
     */
    private static function classNames(): array
    {
        return collect(glob(base_path('app/Form/Fields/*.php')))
            ->filter(fn ($fieldClass) => preg_match('/[A-Za-z]Field\.php$/', $fieldClass) === 1)
            ->map(fn ($fieldClass) => str($fieldClass)->replace(base_path(''), '')->replace('/app', '/App')->replace('.php', '')->replace('/', '\\')->toString())
            ->values()
            ->toArray();
    }

    public static function classFromType(string $type): ?string
    {
        /** @var class-string<Field> */
        $fieldClass = '\\App\\Form\\Fields\\' . $type;
        if (!class_exists($fieldClass)) {
            return null;
        }

        return $fieldClass;
    }

    /**
     * @return array<string, string>
     */
    public static function metaAttributes(): array
    {
        return collect(static::meta())->mapWithKeys(fn ($meta) => [$meta['key'] => $meta['label']])->toArray();
    }

    /**
     * @return array<string, mixed>
     **/
    public static function metaRules(): array
    {
        $result = [];
        foreach (static::meta() as $meta) {
            foreach ($meta['rules'] as $fieldName => $rules) {
                $result[$fieldName] = $rules;
            }
        }

        return $result;
    }

    public static function type(): string
    {
        return class_basename(static::class);
    }

    /**
     * @return array<string, mixed>
     */
    public static function allMeta(): array
    {
        return [
            'id' => static::type(),
            'name' => static::name(),
            'default' => [
                'name' => '',
                'type' => static::type(),
                'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
                'default' => static::default(),
                'required' => false,
                ...collect(static::meta())->mapWithKeys(fn ($meta) => [$meta['key'] => $meta['default']])->toArray(),
            ],
        ];
    }
}
