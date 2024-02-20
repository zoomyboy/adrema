<?php

namespace Tests\EndToEnd\Form;

use Tests\EndToEndTestCase;
use Tests\Lib\CreatesFormFields;

abstract class FormTestCase extends EndToEndTestCase
{
    use CreatesFormFields;
}
