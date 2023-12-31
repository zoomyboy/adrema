<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Form\Resources\FormResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class FormIndexAction
{
    use AsAction;

    /**
     * @return LengthAwarePaginator<Form>
     */
    public function handle(): LengthAwarePaginator
    {
        return Form::paginate(15);
    }

    public function asController(): Response
    {
        return Inertia::render('form/Index', [
            'data' => FormResource::collection($this->handle()),
        ]);
    }
}
