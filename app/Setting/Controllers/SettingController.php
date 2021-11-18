<?php

namespace App\Setting\Controllers;

use App\Http\Controllers\Controller;
use App\Setting\GeneralSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{

    /**
     * @wip
     */
    public function index(GeneralSettings $generalSettings): Response
    {
        return Inertia::render('setting/Index', [
            'options' => [
                'modes' => $generalSettings->modeOptions()
            ],
            'general' => [
                'modes' => $generalSettings->modes
            ]
        ]);
    }

}
