<?php

use App\Contribution\Actions\GenerateApiAction as ContributionGenerateApiAction;
use App\Form\Actions\FormApiListAction;
use App\Form\Actions\RegisterAction;
use App\Prevention\Actions\SettingStoreAction as PreventionStoreAction;
use App\Group\Actions\GroupApiIndexAction;
use App\Prevention\Actions\SettingApiAction;

Route::post('/contribution-generate', ContributionGenerateApiAction::class)->name('api.contribution.generate')->middleware('client:contribution-generate');
Route::post('/form/{form}/register', RegisterAction::class)->name('form.register');
Route::get('/group/{group?}', GroupApiIndexAction::class)->name('api.group');
Route::get('/form', FormApiListAction::class)->name('api.form.index');
Route::get('/prevention', SettingApiAction::class)->name('api.prevention.index');
Route::post('/prevention', PreventionStoreAction::class)->name('api.prevention.store');
