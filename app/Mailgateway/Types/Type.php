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

    abstract public function createList(string $name, string $domain): void;

    abstract public function remove(string $name, string $domain, string $email): void;

    /**
     * @return Collection<int, MailEntry>
     */
    abstract public function list(string $name, string $domain): Collection;

    /**
     * @param array<string, mixed> $params
     */
    abstract public function setParams(array $params): static;

    /**
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return collect(static::fields())->mapWithKeys(fn ($field) => [
            $field['name'] => $field['default'],
        ])->toArray();
    }

    /**
     * @return array<int, MailgatewayParsedCustomField>
     */
    public static function presentFields(string $validator): array
    {
        return array_map(fn ($field) => [
            ...$field,
            'is_required' => str_contains($field[$validator], 'required'),
        ], static::fields());
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(string $validator): array
    {
        return collect(static::fields())->mapWithKeys(fn ($field) => [
            $field['name'] => $field[$validator],
        ])->toArray();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
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
        $members = $this->list($name, $domain);
        foreach ($results as $result) {
            if ($members->first(fn ($member) => $member->is($result))) {
                continue;
            }

            $this->add($name, $domain, $result->email);
        }

        $this->list($name, $domain)
             ->filter(fn ($listEntry) => $results->doesntContain(fn ($r) => $r->is($listEntry)))
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
