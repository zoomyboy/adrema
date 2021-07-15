<?php

use App\Http\Controllers\HomeController;
use App\Initialize\InitializeController;
use App\Member\MemberConfirmController;
use App\Member\MemberController;
use App\Payment\AllpaymentController;
use App\Payment\PaymentController;
use App\Payment\SubscriptionController;
use App\Pdf\MemberPdfController;

Route::group(['namespace' => 'App\\Http\\Controllers'], function() {
    Auth::routes(['register' => false]);
});

Route::group(['middleware' => 'auth:web'], function () {
    Route::get('/', HomeController::class)->name('home');
    Route::resource('initialize', InitializeController::class);
    Route::resource('member', MemberController::class);
    Route::resource('member.payment', PaymentController::class);
    Route::resource('allpayment', AllpaymentController::class);
    Route::resource('subscription', SubscriptionController::class);
    Route::post('/member/{member}/confirm', MemberConfirmController::class);
    Route::get('/member/{member}/pdf', MemberPdfController::class)
        ->name('member.singlepdf');
});

