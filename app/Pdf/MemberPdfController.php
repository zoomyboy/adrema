<?php

namespace App\Pdf;

use App\Http\Controllers\Controller;
use App\Letter\DocumentFactory;
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
        $document = app(DocumentFactory::class)->fromSingleRequest($request->type, $member);

        return null === $document
            ? response()->noContent()
            : Tex::compile($document);
    }
}
