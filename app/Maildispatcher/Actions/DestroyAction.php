<?php

namespace App\Maildispatcher\Actions;

use App\Maildispatcher\Models\Maildispatcher;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroyAction
{
    use AsAction;

    public function handle(Maildispatcher $maildispatcher): void
    {
        $maildispatcher->delete();
    }

    public function asController(Maildispatcher $maildispatcher): RedirectResponse
    {
        $this->handle($maildispatcher);

        return redirect()->back();
    }
}
