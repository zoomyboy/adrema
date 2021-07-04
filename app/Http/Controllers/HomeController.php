<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Views\HomeView;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        session()->put('menu', 'dashboard');
        session()->put('title', 'Dashboard');

        return \Inertia::render('home/Index', app(HomeView::class)->index($request));
    }
}
