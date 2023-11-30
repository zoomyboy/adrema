<?php

namespace App\Payment;

use App\Http\Controllers\Controller;
use App\Invoice\BillDocument;
use App\Invoice\BillKind;
use App\Invoice\DocumentFactory;
use App\Invoice\Queries\BillKindQuery;
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
        $memberCollection = (new BillKindQuery(BillKind::POST))->type($request->type)->getMembers();

        if ($memberCollection->isEmpty()) {
            return response()->noContent();
        }

        $documents = $memberCollection->map(function ($members) use ($request) {
            $document = $request->type::fromMembers($members);
            app(DocumentFactory::class)->afterSingle($document, $members);
            return $document;
        });


        return Tex::merge($documents->all());
    }
}
