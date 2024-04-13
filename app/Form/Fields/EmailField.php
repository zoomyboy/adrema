<?php

namespace App\Form\Fields;

use App\Form\Models\Form;

class EmailField extends TextField
{

    public static function name(): string
    {
        return 'E-Mail-Adresse';
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationRules(Form $form): array
    {
        return [$this->key => $this->required ? ['required', 'string', 'email'] : ['nullable', 'email', 'string']];
    }
}
