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
use App\Contribution\Actions\GenerateAction as ContributionGenerateAction;
use App\Contribution\Actions\ValidateAction as ContributionValidateAction;
use App\Course\Controllers\CourseController;
use App\Dashboard\Actions\IndexAction as DashboardIndexAction;
use App\Efz\ShowEfzDocumentAction;
use App\Group\Actions\ListAction;
use App\Initialize\Actions\InitializeAction;
use App\Initialize\Actions\InitializeFormAction;
use App\Initialize\Actions\NamiGetSearchLayerAction;
use App\Initialize\Actions\NamiLoginCheckAction;
use App\Initialize\Actions\NamiSearchAction;
use App\Maildispatcher\Actions\CreateAction;
use App\Maildispatcher\Actions\DestroyAction;
use App\Maildispatcher\Actions\EditAction;
use App\Maildispatcher\Actions\IndexAction;
use App\Maildispatcher\Actions\StoreAction as MaildispatcherStoreAction;
use App\Maildispatcher\Actions\UpdateAction as MaildispatcherUpdateAction;
use App\Mailgateway\Actions\StoreAction;
use App\Mailgateway\Actions\UpdateAction;
use App\Member\Actions\ExportAction;
use App\Member\Actions\MemberDeleteAction;
use App\Member\Actions\MemberResyncAction;
use App\Member\Actions\MemberShowAction;
use App\Member\Actions\SearchAction;
use App\Member\MemberController;
use App\Membership\Actions\IndexAction as MembershipIndexAction;
use App\Membership\Actions\ListForGroupAction;
use App\Membership\Actions\MembershipDestroyAction;
use App\Membership\Actions\MembershipStoreAction;
use App\Membership\Actions\MembershipUpdateAction;
use App\Membership\Actions\StoreForGroupAction;
use App\Payment\Actions\AllpaymentPageAction;
use App\Payment\Actions\AllpaymentStoreAction;
use App\Payment\Actions\ApiIndexAction as PaymentApiIndexAction;
use App\Payment\PaymentController;
use App\Payment\SendpaymentController;
use App\Payment\SubscriptionController;
use App\Pdf\MemberPdfController;

Route::group(['namespace' => 'App\\Http\\Controllers'], function (): void {
    Auth::routes(['register' => false]);
});

Route::group(['middleware' => 'auth:web'], function (): void {
    Route::get('/', DashboardIndexAction::class)->name('home');
    Route::post('/nami/login-check', NamiLoginCheckAction::class)->name('nami.login-check');
    Route::post('/nami/get-search-layer', NamiGetSearchLayerAction::class)->name('nami.get-search-layer');
    Route::post('/nami/search', NamiSearchAction::class)->name('nami.search');
    Route::get('/initialize', InitializeFormAction::class)->name('initialize.form');
    Route::post('/initialize', InitializeAction::class)->name('initialize.store');
    Route::resource('member', MemberController::class)->except('show', 'destroy');
    Route::delete('/member/{member}', MemberDeleteAction::class);
    Route::get('/member/{member}', MemberShowAction::class)->name('member.show');
    Route::get('allpayment', AllpaymentPageAction::class)->name('allpayment.page');
    Route::post('allpayment', AllpaymentStoreAction::class)->name('allpayment.store');
    Route::resource('subscription', SubscriptionController::class);
    Route::get('/member/{member}/pdf', MemberPdfController::class)
        ->name('member.singlepdf');
    Route::get('/sendpayment', [SendpaymentController::class, 'create'])->name('sendpayment.create');
    Route::get('/sendpayment/pdf', [SendpaymentController::class, 'send'])->name('sendpayment.pdf');
    Route::resource('member.course', CourseController::class);
    Route::get('/member/{member}/efz', ShowEfzDocumentAction::class)->name('efz');
    Route::get('/member/{member}/resync', MemberResyncAction::class)->name('member.resync');
    Route::get('member-export', ExportAction::class)->name('member-export');
    Route::get('/activity', ActivityIndexAction::class)->name('activity.index');
    Route::get('/activity/{activity}/edit', ActivityEditAction::class)->name('activity.edit');
    Route::get('/activity/create', ActivityCreateAction::class)->name('activity.create');
    Route::post('/activity', ActivityStoreAction::class)->name('activity.store');
    Route::patch('/activity/{activity}', ActivityUpdateAction::class)->name('activity.update');
    Route::delete('/activity/{activity}', ActivityDestroyAction::class)->name('activity.destroy');
    Route::post('/subactivity', SubactivityStoreAction::class)->name('api.subactivity.store');
    Route::patch('/subactivity/{subactivity}', SubactivityUpdateAction::class)->name('api.subactivity.update');
    Route::get('/subactivity/{subactivity}', SubactivityShowAction::class)->name('api.subactivity.show');
    Route::post('/api/member/search', SearchAction::class)->name('member.search');

    // ------------------------------- Contributions -------------------------------
    Route::get('/contribution', ContributionFormAction::class)->name('contribution.form');
    Route::get('/contribution-generate', ContributionGenerateAction::class)->name('contribution.generate');
    Route::post('/contribution-validate', ContributionValidateAction::class)->name('contribution.validate');

    // ----------------------------------- mail ------------------------------------
    Route::post('/api/mailgateway', StoreAction::class)->name('mailgateway.store');
    Route::patch('/api/mailgateway/{mailgateway}', UpdateAction::class)->name('mailgateway.update');
    Route::get('/maildispatcher', IndexAction::class)->name('maildispatcher.index');
    Route::get('/maildispatcher/create', CreateAction::class)->name('maildispatcher.create');
    Route::get('/maildispatcher/{maildispatcher}', EditAction::class)->name('maildispatcher.edit');
    Route::patch('/maildispatcher/{maildispatcher}', MaildispatcherUpdateAction::class)->name('maildispatcher.update');
    Route::post('/maildispatcher', MaildispatcherStoreAction::class)->name('maildispatcher.store');
    Route::delete('/maildispatcher/{maildispatcher}', DestroyAction::class)->name('maildispatcher.destroy');

    // ----------------------------------- group -----------------------------------
    Route::get('/group', ListAction::class)->name('group.index');

    // ---------------------------------- payment ----------------------------------
    Route::apiResource('member.payment', PaymentController::class);
    Route::post('/api/member/{member}/payment', PaymentApiIndexAction::class)->name('member.payment.index');

    // --------------------------------- membership --------------------------------
    Route::get('/member/{member}/membership', MembershipIndexAction::class)->name('member.membership.index');
    Route::post('/member/{member}/membership', MembershipStoreAction::class)->name('member.membership.store');
    Route::patch('/membership/{membership}', MembershipUpdateAction::class)->name('membership.update');
    Route::delete('/membership/{membership}', MembershipDestroyAction::class)->name('membership.destroy');
    Route::post('/api/membership/member-list', ListForGroupAction::class)->name('membership.member-list');
    Route::post('/api/membership/sync', StoreForGroupAction::class)->name('membership.sync');
});
