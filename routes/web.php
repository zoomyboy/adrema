<?php

use App\Bill\SettingIndexAction as BillSettingIndexAction;
use App\Bill\SettingSaveAction as BillSettingSaveAction;
use App\Contribution\ContributionController;
use App\Course\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Initialize\Actions\InitializeAction;
use App\Initialize\Actions\InitializeFormAction;
use App\Member\Controllers\MemberResyncController;
use App\Member\MemberConfirmController;
use App\Member\MemberController;
use App\Membership\MembershipController;
use App\Payment\AllpaymentController;
use App\Payment\PaymentController;
use App\Payment\SendpaymentController;
use App\Payment\SubscriptionController;
use App\Pdf\MemberEfzController;
use App\Pdf\MemberPdfController;

Route::group(['namespace' => 'App\\Http\\Controllers'], function (): void {
    Auth::routes(['register' => false]);
});

Route::group(['middleware' => 'auth:web'], function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/initialize', InitializeFormAction::class)->name('initialize.form');
    Route::post('/initialize', InitializeAction::class)->name('initialize.store');
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
    Route::get('setting/bill', BillSettingIndexAction::class);
    Route::post('setting/bill', BillSettingSaveAction::class);
    Route::resource('member.course', CourseController::class);
    Route::get('/member/{member}/efz', MemberEfzController::class)->name('efz');
    Route::get('/member/{member}/resync', MemberResyncController::class)->name('member.resync');
    Route::get('/contribution', [ContributionController::class, 'form'])->name('contribution.form');
    Route::get('/contribution/generate', [ContributionController::class, 'generate'])->name('contribution.generate');
});
