<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use App\Providers\CustomUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

            Auth::provider('custom', function ($app, array $config) {
        return new CustomUserProvider($app['hash'], $config['model']);
    });
    }
}
