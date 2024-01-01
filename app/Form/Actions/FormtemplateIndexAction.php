<?php

namespace App\Form\Actions;

use App\Form\Models\Formtemplate;
use App\Form\Resources\FormtemplateResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class FormtemplateIndexAction
{
    use AsAction;

    /**
     * @return LengthAwarePaginator<Formtemplate>
     */
    public function handle(): LengthAwarePaginator
    {
        return Formtemplate::paginate(15);
    }

    public function asController(): Response
    {
        session()->put('menu', 'form');
        session()->put('title', 'Formular-Vorlagen');

        return Inertia::render('formtemplate/Index', [
            'data' => FormtemplateResource::collection($this->handle()),
        ]);
    }
}
