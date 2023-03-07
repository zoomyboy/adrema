<?php

use App\Activity\Actions\ActivityStoreAction;
use App\Activity\Actions\ActivityUpdateAction;
use App\Activity\Actions\CreateAction as ActivityCreateAction;
use App\Activity\Actions\DestroyAction as ActivityDestroyAction;
use App\Activity\Actions\EditAction as ActivityEditAction;
use App\Activity\Actions\IndexAction as ActivityIndexAction;
use App\Activity\Api\SubactivityShowAction;
use App\Activity\Api\SubactivityStoreAction;
use App\Activity\Api\SubactivityUpdateAction;
use App\Contribution\Actions\FormAction as ContributionFormAction;
use App\Contribution\ContributionController;
use App\Course\Controllers\CourseController;
use App\Efz\ShowEfzDocumentAction;
use App\Home\Actions\IndexAction as HomeIndexAction;
use App\Initialize\Actions\InitializeAction;
use App\Initialize\Actions\InitializeFormAction;
use App\Member\Actions\MemberResyncAction;
use App\Member\Actions\MemberShowAction;
use App\Member\Actions\SearchAction;
use App\Member\MemberController;
use App\Membership\Actions\MembershipDestroyAction;
use App\Membership\Actions\MembershipStoreAction;
use App\Membership\Actions\MembershipUpdateAction;
use App\Payment\Actions\AllpaymentPageAction;
use App\Payment\Actions\AllpaymentStoreAction;
use App\Payment\PaymentController;
use App\Payment\SendpaymentController;
use App\Payment\SubscriptionController;
use App\Pdf\MemberPdfController;

Route::group(['namespace' => 'App\\Http\\Controllers'], function (): void {
    Auth::routes(['register' => false]);
});

Route::group(['middleware' => 'auth:web'], function (): void {
    Route::get('/', HomeIndexAction::class)->name('home');
    Route::post('/api/member/search', SearchAction::class)->name('member.search');
    Route::get('/initialize', InitializeFormAction::class)->name('initialize.form');
    Route::post('/initialize', InitializeAction::class)->name('initialize.store');
    Route::resource('member', MemberController::class)->except('show');
    Route::get('/member/{member}', MemberShowAction::class)->name('member.show');
    Route::apiResource('member.payment', PaymentController::class);
    Route::get('allpayment', AllpaymentPageAction::class)->name('allpayment.page');
    Route::post('allpayment', AllpaymentStoreAction::class)->name('allpayment.store');
    Route::resource('subscription', SubscriptionController::class);
    Route::get('/member/{member}/pdf', MemberPdfController::class)
        ->name('member.singlepdf');
    Route::get('/sendpayment', [SendpaymentController::class, 'create'])->name('sendpayment.create');
    Route::get('/sendpayment/pdf', [SendpaymentController::class, 'send'])->name('sendpayment.pdf');
    Route::post('/member/{member}/membership', MembershipStoreAction::class)->name('membership.store');
    Route::patch('/member/{member}/membership/{membership}', MembershipUpdateAction::class)->name('membership.store');
    Route::delete('/member/{member}/membership/{membership}', MembershipDestroyAction::class)->name('membership.destroy');
    Route::resource('member.course', CourseController::class);
    Route::get('/member/{member}/efz', ShowEfzDocumentAction::class)->name('efz');
    Route::get('/member/{member}/resync', MemberResyncAction::class)->name('member.resync');
    Route::get('/contribution', ContributionFormAction::class)->name('contribution.form');
    Route::get('/contribution/generate', [ContributionController::class, 'generate'])->name('contribution.generate');
    Route::get('/activity', ActivityIndexAction::class)->name('activity.index');
    Route::get('/activity/{activity}/edit', ActivityEditAction::class)->name('activity.edit');
    Route::get('/activity/create', ActivityCreateAction::class)->name('activity.create');
    Route::post('/activity', ActivityStoreAction::class)->name('activity.store');
    Route::patch('/activity/{activity}', ActivityUpdateAction::class)->name('activity.update');
    Route::delete('/activity/{activity}', ActivityDestroyAction::class)->name('activity.destroy');
    Route::post('/subactivity', SubactivityStoreAction::class)->name('api.subactivity.store');
    Route::patch('/subactivity/{subactivity}', SubactivityUpdateAction::class)->name('api.subactivity.update');
    Route::get('/subactivity/{subactivity}', SubactivityShowAction::class)->name('api.subactivity.show');
});
