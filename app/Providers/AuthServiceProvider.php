<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Policies\DepartmentPolicy;
use App\Policies\InstrumentPolicy;
use App\Policies\InstrumentsRequestPolicy;
use App\Policies\StorePolicy;
use App\Policies\UserPolicy;
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
        //Users
        Gate::define("create-user", [UserPolicy::class, "create"]);

        //Departments
        Gate::define("create-department", [DepartmentPolicy::class, "create"]);

        //Stores
        Gate::define("create-store", [StorePolicy::class, "create"]);

        //Instruments
        Gate::define("create-instrument", [InstrumentPolicy::class, "create"]);

        //Instruments request
        Gate::define("request-instruments", [InstrumentsRequestPolicy::class, "create"]);
    }
}
