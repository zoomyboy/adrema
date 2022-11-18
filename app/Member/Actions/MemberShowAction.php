<?php

namespace App\Member\Actions;

use App\Member\Member;
use App\Member\MemberResource;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class MemberShowAction
{
    use AsAction;

    /**
     * @return array{data: MemberResource}
     */
    public function handle(Member $member): array
    {
        return [
            'data' => new MemberResource($member->load('memberships')),
            'toolbar' => [['href' => route('member.index'), 'label' => 'Zurück', 'color' => 'primary', 'icon' => 'undo']],
        ];
    }

    public function asController(Member $member): Response
    {
        session()->put('menu', 'member');
        session()->put('title', 'Mitglied '.$member->fullname);

        return Inertia::render('member/Show', $this->handle($member));
    }
}
