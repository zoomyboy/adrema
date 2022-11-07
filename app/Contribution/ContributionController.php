<?php

namespace App\Contribution;

use App\Country;
use App\Http\Controllers\Controller;
use App\Member\Member;
use App\Member\MemberResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Tex;

class ContributionController extends Controller
{
    public function form(): Response
    {
        session()->put('menu', 'contribution');
        session()->put('title', 'Zuschüsse');

        return Inertia::render('contribution/VIndex', [
            'allMembers' => MemberResource::collection(Member::slangOrdered()->get()),
            'countries' => Country::pluck('name', 'id'),
            'defaultCountry' => Country::firstWhere('name', 'Deutschland')->id,
        ]);
    }

    public function generate(Request $request): BaseCompiler
    {
        /** @var class-string<SolingenDocument> */
        $type = $request->query('type');

        return Tex::compile($type::fromRequest($request));
    }
}
