<?php

namespace App\Pdf;

use App\Http\Controllers\Controller;
use App\Member\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MemberPdfController extends Controller
{

    public function __invoke(Request $request, Member $member)
    {
        $repo = app(PdfRepositoryFactory::class)->fromSingleRequest($request->type, $member);

        return $repo === null
            ? response()->noContent()
            : app(PdfGenerator::class)->setRepository($repo)->render();
    }

}
