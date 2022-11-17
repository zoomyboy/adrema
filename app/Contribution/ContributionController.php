<?php

namespace App\Contribution;

use App\Contribution\Documents\SolingenDocument;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Tex;

class ContributionController extends Controller
{
    public function generate(Request $request): BaseCompiler
    {
        /** @var class-string<SolingenDocument> */
        $type = $request->query('type');

        return Tex::compile($type::fromRequest($request));
    }
}
