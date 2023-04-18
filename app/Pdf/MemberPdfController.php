<?php

namespace App\Pdf;

use App\Http\Controllers\Controller;
use App\Invoice\DocumentFactory;
use App\Invoice\Queries\SingleMemberQuery;
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
        $invoice = app(DocumentFactory::class)->singleInvoice($request->type, new SingleMemberQuery($member));

        return null === $invoice
            ? response()->noContent()
            : Tex::compile($invoice);
    }
}
