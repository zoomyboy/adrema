<?php

namespace App\Form\Actions;

use App\Form\FormSettings;
use App\Form\Models\Form;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Lorisleiva\Actions\Concerns\AsAction;

class FormGenerateLaterlinkAction
{
    use AsAction;

    public function asController(Form $form)
    {
        $registerUrl = str(app(FormSettings::class)->registerUrl)->replace('{slug}', $form->slug)->toString();
        $laterId = str()->uuid()->toString();
        $laterUrl = URL::signedRoute('form.register', ['form' => $form, 'later' => '1', 'id' => $laterId]);
        $urlParts = parse_url($laterUrl);

        Cache::remember('later_'.$laterId, 2592000, fn () => $form->id);    // Link ist 40 Tage gültig

        return response()->json([
            'url' => $registerUrl.'?'.data_get($urlParts, 'query')
        ]);
    }
}
