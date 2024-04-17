<?php

namespace Tests\Feature\Form;

use App\Form\Fields\Field;
use App\Form\Enums\NamiType;
use Worksome\RequestFactories\RequestFactory;
use App\Form\Enums\SpecialType;

/**
 * @method self name(string $name)
 * @method self key(string $key)
 * @method self required(string|bool $key)
 * @method self rows(int $rows)
 * @method self columns(array{mobile: int, tablet: int, desktop: int} $rows)
 * @method self default(mixed $default)
 * @method self options(array<int, string> $options)
 * @method self maxToday(bool $maxToday)
 * @method self parentGroup(int $groupId)
 * @method self parentField(string $fieldKey)
 * @method self namiType(?NamiType $type)
 * @method self forMembers(bool $forMembers)
 * @method self specialType(SpecialType $specialType)
 * @method self hint(string $hint)
 * @method self min(int $min)
 * @method self max(int $max)
 * @method self allowcustom(bool $allowcustom)
 */
class FormtemplateFieldRequest extends RequestFactory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(5, true),
            'key' => str($this->faker->words(5, true))->snake()->toString(),
            'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
            'nami_type' => null,
            'for_members' => true,
            'hint' => '',
        ];
    }

    /**
     * @param string|class-string<Field> $field
     */
    public static function type(string $field): self
    {

        if (!$field || !class_exists($field)) {
            return self::new(['type' => $field]);
        }

        return self::new([
            'type' => $field::type(),
            'value' => $field::default(),
            ...$field::fake((new static())->faker),
        ]);
    }

    /**
     * @param mixed $args
     */
    public function __call(string $method, $args): self
    {
        return $this->state([(string) str($method)->snake() => $args[0]]);
    }
}
