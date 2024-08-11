<?php

namespace Nrm\ActivityTracker\Providers;
use Illuminate\Support\ServiceProvider;
use Nrm\ActivityTracker\Middleware\ActivityTracker;

use Nrm\ActivityTracker\Traits\PublishMigrationTrait;

class ActivityTrackerProvider extends ServiceProvider
{
    use PublishMigrationTrait;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMigrations(__DIR__.'/../database/migrations');
        $this->app['router']->aliasMiddleware('nrm-activity-tracker', ActivityTracker::class);
    }
}
