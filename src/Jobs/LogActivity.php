<?php

namespace Nrm\ActivityTracker\Jobs;

use App\Models;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Nrm\ActivityTracker\Models\ActivityLog;

class LogActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $activityData;

    public function __construct(array $activityData)
    {
        $this->activityData = $activityData;
    }

    public function handle()
    {
        ActivityLog::insert($this->activityData);
    }
}
