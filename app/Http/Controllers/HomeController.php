<?php

namespace App\Http\Controllers;

use App\Http\Views\HomeView;
use Illuminate\Http\Request;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        session()->put('menu', 'dashboard');
        session()->put('title', 'Dashboard');

        return \Inertia::render('home/VIndex', app(HomeView::class)->index($request));
    }
}
