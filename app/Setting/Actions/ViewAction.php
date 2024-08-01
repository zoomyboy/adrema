<?php

namespace App\Setting\Actions;

use App\Setting\LocalSettings;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ViewAction
{
    use AsAction;

    public function handle(LocalSettings $settingGroup): Response
    {
        session()->put('menu', 'setting');
        session()->put('title', $settingGroup::title());

        return Inertia::render('setting/' . ucfirst($settingGroup::group()), $settingGroup->viewData());
    }
}
