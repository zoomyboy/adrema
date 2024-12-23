<?php

namespace App\Form\Fields;

use App\Form\Data\ColumnData;
use App\Form\Enums\NamiType;
use App\Form\Enums\SpecialType;
use App\Form\Matchers\Matcher;
use App\Form\Matchers\SingleValueMatcher;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Presenters\DefaultPresenter;
use App\Form\Presenters\Presenter;
use App\Lib\Editor\Comparator;
use Faker\Generator;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
abstract class Field extends Data
{

    public string $key;
    public string $name;
    public ?NamiType $namiType = null;
    public ColumnData $columns;
    public bool $forMembers;
    public ?SpecialType $specialType = null;
    public ?string $hint;
    public ?string $intro;

    /** @var mixed */
    public $value;

    /**
     * @param array<array-key, mixed> $input
     */
    abstract public function afterRegistration(Form $form, Participant $participant, array $input): void;

    abstract public static function name(): string;

    /** @return array<int, array{key: string, default: mixed, label: string, rules: array<string, mixed>}> */
    abstract public static function meta(): array;

    /** @return mixed */
    abstract public static function default();

    /** @return array<string, mixed> */
    abstract public function getRegistrationRules(Form $form): array;

    /** @return array<string, mixed> */
    abstract public function getRegistrationAttributes(Form $form): array;

    /** @return array<string, mixed> */
    abstract public function getRegistrationMessages(Form $form): array;

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
            ->map(fn ($fieldClass) => str($fieldClass)->replaceFirst(base_path(''), '')->replace('/app', '/App')->replace('.php', '')->replace('/', '\\')->toString())
            ->values()
            ->toArray();
    }

    /**
     * @return class-string<Field>
     */
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
     * @return mixed
     */
    public function present()
    {
        return [
            $this->key => $this->value,
            $this->getDisplayAttribute() => $this->presentRaw(),
        ];
    }

    public function presentRaw(): string
    {
        return $this->getPresenter()->present($this->value);
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
                'value' => static::default(),
                'nami_type' => null,
                'for_members' => true,
                'special_type' => null,
                'hint' => '',
                'intro' => '',
                ...collect(static::meta())->mapWithKeys(fn ($meta) => [$meta['key'] => $meta['default']])->toArray(),
            ],
        ];
    }

    public function getPresenter(): Presenter
    {
        return app(DefaultPresenter::class);
    }

    public function getDisplayAttribute(): string
    {
        return $this->key . '_display';
    }

    public function matches(Comparator $comparator, mixed $value): bool
    {
        return $this->getMatcher()->setValue($this->value)->matches($comparator, $value);
    }

    public function getMatcher(): Matcher
    {
        return app(SingleValueMatcher::class);
    }

    /** @param mixed $value */
    public function filter($value): string
    {
        return '';
    }
}
