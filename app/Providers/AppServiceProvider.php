<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\InfraAuditService;
use App\Models\InfraAudit;
use App\Policies\InfraAuditPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{


    protected $policies = [
        // ... your existing policies ...
        InfraAudit::class => InfraAuditPolicy::class,
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(InfraAuditService::class, function ($app) {
            return new InfraAuditService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
