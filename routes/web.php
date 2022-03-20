<?php

use App\Course\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Initialize\InitializeController;
use App\Member\MemberConfirmController;
use App\Member\MemberController;
use App\Membership\MembershipController;
use App\Payment\AllpaymentController;
use App\Payment\PaymentController;
use App\Payment\SendpaymentController;
use App\Payment\SubscriptionController;
use App\Pdf\MemberEfzController;
use App\Pdf\MemberPdfController;
use App\Setting\Controllers\SettingController;

Route::group(['namespace' => 'App\\Http\\Controllers'], function (): void {
    Auth::routes(['register' => false]);
});

Route::group(['middleware' => 'auth:web'], function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::resource('initialize', InitializeController::class);
    Route::resource('member', MemberController::class);
    Route::resource('member.payment', PaymentController::class);
    Route::resource('allpayment', AllpaymentController::class);
    Route::resource('subscription', SubscriptionController::class);
    Route::post('/member/{member}/confirm', MemberConfirmController::class);
    Route::get('/member/{member}/pdf', MemberPdfController::class)
        ->name('member.singlepdf');
    Route::get('/sendpayment', [SendpaymentController::class, 'create'])->name('sendpayment.create');
    Route::get('/sendpayment/pdf', [SendpaymentController::class, 'send'])->name('sendpayment.pdf');
    Route::apiResource('member.membership', MembershipController::class);
    Route::resource('setting', SettingController::class);
    Route::resource('member.course', CourseController::class);
    Route::get('/member/{member}/efz', MemberEfzController::class)->name('efz');
});
