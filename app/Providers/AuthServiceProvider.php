<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::tokensExpireIn(now()->addYears(999));
        Passport::refreshTokensExpireIn(now()->addYears(999));
        Passport::personalAccessTokensExpireIn(now()->addYears(999));
        Passport::tokensCan([
            'contribution-generate' => 'Create Contribution PDFs',
        ]);
    }
}
