<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Constants\AppConstant;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::before(function (User $user, $permission) {
            if($user->hasRoleWithDepartment(AppConstant::ADMINISTRATOR, $user->department_id)){
                return true;
            }
            return $user->hasPermissionTo($permission);
        });
    }
}