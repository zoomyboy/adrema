<?php

namespace App\Pdf;

enum EnvType: string
{
    case XELATEX = 'XELATEX';
    case PDFLATEX = 'PDFLATEX';
}
