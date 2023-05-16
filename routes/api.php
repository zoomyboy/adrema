<?php

use App\Contribution\Actions\GenerateApiAction as ContributionGenerateApiAction;

Route::post('/contribution-generate', ContributionGenerateApiAction::class)->name('api.contribution.generate')->middleware('client:contribution-generate');
