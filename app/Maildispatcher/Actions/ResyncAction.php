<?php

namespace App\Maildispatcher\Actions;

use App\Maildispatcher\Data\MailEntry;
use App\Maildispatcher\Models\Maildispatcher;
use App\Member\FilterScope;
use App\Member\Member;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class ResyncAction
{
    use AsAction;

    public function handle(): void
    {
        foreach (Maildispatcher::get() as $dispatcher) {
            $dispatcher->gateway->type->sync($dispatcher->name, $dispatcher->gateway->domain, $this->getResults($dispatcher));
        }
    }

    /**
     * @return Collection<int, MailEntry>
     */
    public function getResults(Maildispatcher $dispatcher): Collection
    {
        return Member::search(data_get($dispatcher->filter, 'search', ''))->query(
            fn ($q) => $q->select('*')->withFilter(FilterScope::fromPost($dispatcher->filter))
        )->get()
            ->filter(fn ($member) => $member->email || $member->email_parents)
            ->map(fn ($member) => MailEntry::from(['email' => $member->email ?: $member->email_parents]))
            ->unique(fn ($member) => $member->email);
    }
}
