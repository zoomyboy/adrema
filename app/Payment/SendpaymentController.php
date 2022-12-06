<?php

namespace App\Payment;

use App\Http\Controllers\Controller;
use App\Letter\BillKind;
use App\Letter\DocumentFactory;
use App\Letter\Queries\BillKindQuery;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Zoomyboy\Tex\Tex;

class SendpaymentController extends Controller
{
    public function create(): InertiaResponse
    {
        session()->put('menu', 'member');
        session()->put('title', 'Rechnungen versenden');

        return Inertia::render('sendpayment/VForm', [
            'types' => app(ActionFactory::class)->allLinks(),
        ]);
    }

    /**
     * @return Response|Responsable
     */
    public function send(Request $request)
    {
        $letter = app(DocumentFactory::class)->singleLetter($request->type, new BillKindQuery(BillKind::POST));

        if (is_null($letter)) {
            return response()->noContent();
        }

        $pdfFile = Tex::compile($letter);
        app(DocumentFactory::class)->afterSingle($letter);

        return $pdfFile;
    }
}
