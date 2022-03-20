<?php

namespace App\Pdf;

use App\Http\Controllers\Controller;
use App\Pdf\Data\MemberEfzData;

class MemberEfzController extends Controller
{
    public function __invoke(MemberEfzData $data): PdfGenerator
    {
        return app(PdfGenerator::class)->setRepository($data)->render();
    }
}
