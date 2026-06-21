<?php

namespace App\Providers;

use App\Models\Procedure;
use App\Policies\ProcedurePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Procedure::class, ProcedurePolicy::class);
    }
}
