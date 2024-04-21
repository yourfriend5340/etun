<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as AccessGate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Carbon\Carbon;
use App\Foundation\Auth\AdminEloquentUserProvider;
use App\Models\User;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
         //'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    
    public function boot()
    {
        $this->registerPolicies();

        if (! $this->app->routesAreCached()) {
            Passport::routes();
        }
            Passport::tokensExpireIn(Carbon::now()->addDays(15));

            Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
            Passport::personalAccessTokensExpireIn(now()->addDays(1));
            //config(['auth.guards.api.provider' => 'employees']);

                // 系統管理者 Gate 規則
                Gate::define('admin', function ($user) {
                    return $user->user_group_id === User::ROLE_ADMIN;
                });

                // 最高管理者 Gate 規則
                Gate::define('super_manager', function ($user) {
                    return $user->user_group_id === User::ROLE_SUPER_MANAGER;
                });
        
                // 一般管理者 Gate 規則
                Gate::define('manager', function ($user) {
                    return $user->user_group_id === User::ROLE_MANAGER;
                });
        
                // 一般使用者 Gate 規則
                Gate::define('user', function ($user) {
                    return $user->user_group_id === User::ROLE_USER;
                });

        //
        //Auth::provider('admin-eloquent', function ($app, $config) {
        //    return New \App\Foundation\Auth\AdminEloquentUserProvider($app['hash'], $config['model']);
        //});
                Gate::define('group_admin', function ($user) {
                
                return $user->user_group_id == 1 || $user->user_group_id==2;
                });

                //Gate::define('group_admin','ClockSalary@view');
    }


}
