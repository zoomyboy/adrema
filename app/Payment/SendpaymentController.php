<?php

namespace App\Payment;

use App\Http\Controllers\Controller;
use App\Pdf\PdfGenerator;
use App\Pdf\PdfRepositoryFactory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SendpaymentController extends Controller
{

    public function create(): InertiaResponse
    {
        session()->put('menu', 'member');
        session()->put('title', 'Rechnungen versenden');

        return Inertia::render('sendpayment/Form', [
            'types' => app(ActionFactory::class)->allLinks(),
        ]);
    }

    public function send(Request $request)
    {
        $repo = app(PdfRepositoryFactory::class)->forAll($request->type, 'Post');

        $pdfFile = app(PdfGenerator::class)->setRepository($repo)->render();
        app(PdfRepositoryFactory::class)->afterAll($request->type, 'Post');

        return $repo === null
            ? response()->noContent()
            : $pdfFile;
    }

}
