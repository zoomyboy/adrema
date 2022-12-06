<?php

namespace App\Pdf;

use App\Http\Controllers\Controller;
use App\Letter\DocumentFactory;
use App\Letter\Queries\SingleMemberQuery;
use App\Member\Member;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Zoomyboy\Tex\Tex;

class MemberPdfController extends Controller
{
    /**
     * @return Response|Responsable
     */
    public function __invoke(Request $request, Member $member)
    {
        $letter = app(DocumentFactory::class)->singleLetter($request->type, new SingleMemberQuery($member));

        return null === $letter
            ? response()->noContent()
            : Tex::compile($letter);
    }
}
