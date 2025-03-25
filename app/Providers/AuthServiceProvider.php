<?php

namespace App\Providers;

use App\Models\Demand;
use App\Policies\DemandPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',

        //Demand::class => DemandPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
