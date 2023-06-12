<?php

namespace App\Mailgateway\Types;

use App\Maildispatcher\Data\MailEntry;
use Illuminate\Support\Collection;

abstract class Type
{
    abstract public static function name(): string;

    /**
     * @return array<int, MailgatewayCustomField>
     */
    abstract public static function fields(): array;

    abstract public function works(): bool;

    abstract public function search(string $name, string $domain, string $email): ?MailEntry;

    abstract public function add(string $name, string $domain, string $email): void;

    abstract public function remove(string $name, string $domain, string $email): void;

    /**
     * @return Collection<int, MailEntry>
     */
    abstract public function list(string $name, string $domain): Collection;

    /**
     * @param array<string, mixed> $params
     */
    abstract public function setParams(array $params): static;

    public static function defaults(): array
    {
        return collect(static::fields())->mapWithKeys(fn ($field) => [
            $field['name'] => $field['default'],
        ])->toArray();
    }

    public static function presentFields(string $validator): array
    {
        return array_map(fn ($field) => [
            ...$field,
            'is_required' => str_contains($field[$validator], 'required'),
        ], static::fields());
    }

    public static function rules(string $validator): array
    {
        return collect(static::fields())->mapWithKeys(fn ($field) => [
            $field['name'] => $field[$validator],
        ])->toArray();
    }

    public function toResource(): array
    {
        return [
            'cls' => get_class($this),
            'params' => get_object_vars($this),
        ];
    }

    /**
     * @param Collection<int, MailEntry> $results
     */
    public function sync(string $name, string $domain, Collection $results): void
    {
        foreach ($results as $result) {
            if ($this->search($name, $domain, $result->email)) {
                continue;
            }

            $this->add($name, $domain, $result->email);
        }

        $this->list($name, $domain)
             ->filter(fn ($listEntry) => null === $results->first(fn ($r) => $r->email === $listEntry->email))
             ->each(fn ($listEntry) => $this->remove($name, $domain, $listEntry->email));
    }

    /**
     * @return array<string, string>
     */
    public static function fieldNames(): array
    {
        return collect(static::fields())->mapWithKeys(fn ($field) => [$field['name'] => $field['label']])->toArray();
    }
}
