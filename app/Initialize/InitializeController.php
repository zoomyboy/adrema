<?php

namespace App\Initialize;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InitializeController extends Controller
{
    public function index() {
        return \Inertia::render('Initialize/Index');
    }

    public function store() {
        InitializeJob::dispatch(auth()->user());

        return \Inertia::render('Initialize/Index');
    }
}
