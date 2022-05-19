<?php

namespace App\Contribution;

use App\Http\Controllers\Controller;
use App\Member\Member;
use App\Member\MemberResource;
use App\Pdf\PdfGenerator;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContributionController extends Controller
{
    public function form(): Response
    {
        session()->put('menu', 'contribution');
        session()->put('title', 'Zuschüsse');

        return Inertia::render('contribution/VIndex', [
            'allMembers' => MemberResource::collection(Member::get()),
        ]);
    }

    public function generate(Request $request): PdfGenerator
    {
        $data = app($request->query('type'));

        return app(PdfGenerator::class)->setRepository($data)->render();
    }
}
