<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke()
    {
        session()->put('menu', 'dashboard');
        session()->put('title', 'Dashboard');
        
        return \Inertia::render('Home', []);
    }
}
