<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke()
    {
        session()->put('menu', 'dashboard');

        return \Inertia::render('Home', []);
    }
}
