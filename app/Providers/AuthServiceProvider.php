<?php

namespace App\Providers;

use App\Models\Board;
use App\Models\Card;
use App\Models\Project;
use App\Models\Status;

use App\Policies\BoardPolicy;
use App\Policies\CardPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\StatusPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    protected $policies = [
        Project::class => ProjectPolicy::class,
        Board::class => BoardPolicy::class,
        Status::class => StatusPolicy::class,
        Card::class => CardPolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
