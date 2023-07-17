<?php

namespace App\Mailgateway\Types;

use App\Maildispatcher\Data\MailEntry;
use App\Maildispatcher\Models\Localmaildispatcher;
use Illuminate\Support\Collection;

class LocalType extends Type
{
    public static function name(): string
    {
        return 'Lokal';
    }

    public function works(): bool
    {
        return true;
    }

    public static function fields(): array
    {
        return [];
    }

    public function setParams(array $params): static
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function list(string $name, string $domain): Collection
    {
        return Localmaildispatcher::where('from', "{$name}@{$domain}")->get()->map(fn ($mail) => MailEntry::from(['email' => $mail->to]));
    }

    public function search(string $name, string $domain, string $email): ?MailEntry
    {
        $result = Localmaildispatcher::where('from', "{$name}@{$domain}")->where('to', $email)->first();

        return $result ? MailEntry::from([
            'email' => $result->to,
        ]) : null;
    }

    public function add(string $name, string $domain, string $email): void
    {
        Localmaildispatcher::create([
            'from' => "{$name}@{$domain}",
            'to' => $email,
        ]);
    }

    public function createList(string $name, string $domain): void
    {
    }

    public function deleteList(string $name, string $domain): void
    {
    }

    public function remove(string $name, string $domain, string $email): void
    {
        Localmaildispatcher::where('from', "{$name}@{$domain}")->where('to', $email)->delete();
    }
}
