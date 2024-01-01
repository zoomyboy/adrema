<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Lib\Events\Succeeded;
use Lorisleiva\Actions\Concerns\AsAction;

class FormDestroyAction
{
    use AsAction;

    public function asController(Form $form): void
    {
        $form->delete();

        Succeeded::message('Veranstaltung gelöscht.')->dispatch();
    }
}
