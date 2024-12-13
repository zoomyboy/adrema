<?php

namespace App\Contribution\Traits;

use Zoomyboy\Tex\Engine;

trait HasPdfBackground
{
    public function getEngine(): Engine
    {
        return Engine::PDFLATEX;
    }
}
