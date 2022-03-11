<?php

namespace App\Initialize;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class InitializeController extends Controller
{
    public function index(): Response
    {
        return \Inertia::render('Initialize/VIndex');
    }

    public function store(): RedirectResponse
    {
        InitializeJob::dispatch(auth()->user());

        return redirect()->route('home');
    }
}
