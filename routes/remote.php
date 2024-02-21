<?php

use App\Remote\Actions\LoginAction;
use App\Remote\Actions\SearchAction;

Route::post('/nami/token', LoginAction::class)->name('remote.nami.token');
Route::post('/nami/search', SearchAction::class)->name('remote.nami.search');
