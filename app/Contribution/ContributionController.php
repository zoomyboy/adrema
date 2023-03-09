<?php

namespace App\Contribution;

use App\Contribution\Documents\SolingenDocument;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Tex;

class ContributionController extends Controller
{
    public function generate(Request $request): BaseCompiler
    {
        $payload = json_decode(base64_decode($request->input('payload', '')), true);

        $validated = Validator::make($payload, [
            'dateFrom' => 'required|date|date_format:Y-m-d',
            'dateUntil' => 'required|date|date_format:Y-m-d',
            'country' => 'required|exists:countries,id',
            'eventName' => 'required|max:100',
            'members' => 'array',
            'members.*' => 'integer|exists:members,id',
            'type' => 'required|string',
            'zipLocation' => 'required|string',
        ])->validate();

        /** @var class-string<SolingenDocument> */
        $type = $validated['type'];

        return Tex::compile($type::fromRequest($validated));
    }
}
