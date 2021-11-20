<?php

namespace App\Initialize;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InitializeController extends Controller
{
    public function index() {
        return \Inertia::render('Initialize/Index');
    }

    public function store() {
        InitializeJob::dispatch(auth()->user());

        return redirect()->route('home');
    }
}
