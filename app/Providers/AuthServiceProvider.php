<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use App\Policies\ModulePolicy;
use App\Models\Module;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Module::class => ModulePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
        //
        Gate::define('update', 'App\Policies\ModulePolicy@update');
        Gate::define('view', 'ModulePolicy@view');
    }
}
