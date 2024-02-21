<?php

use App\Contribution\Actions\GenerateApiAction as ContributionGenerateApiAction;
use App\Remote\Actions\LoginAction;

Route::post('/nami/token', LoginAction::class)->name('remote.nami.token');
