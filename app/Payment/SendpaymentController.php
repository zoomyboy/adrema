<?php

namespace App\Payment;

use App\Http\Controllers\Controller;
use App\Letter\DocumentFactory;
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
        $repo = app(DocumentFactory::class)->forAll($request->type, 'Post');

        if (is_null($repo)) {
            return response()->noContent();
        }

        $pdfFile = Tex::compile($repo);
        app(DocumentFactory::class)->afterAll($request->type, 'Post');

        return $pdfFile;
    }
}
