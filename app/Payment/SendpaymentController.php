<?php

namespace App\Payment;

use App\Http\Controllers\Controller;
use App\Pdf\PdfGenerator;
use App\Pdf\PdfRepositoryFactory;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

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
        $repo = app(PdfRepositoryFactory::class)->forAll($request->type, 'Post');

        $pdfFile = app(PdfGenerator::class)->setRepository($repo)->render();
        app(PdfRepositoryFactory::class)->afterAll($request->type, 'Post');

        return null === $repo
            ? response()->noContent()
            : $pdfFile;
    }
}
