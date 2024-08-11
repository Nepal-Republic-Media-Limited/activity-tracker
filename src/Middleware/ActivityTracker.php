<?php

namespace Nrm\ActivityTracker\Middleware;

use Closure;
use Nrm\ActivityTracker\Jobs\LogActivity;
use Illuminate\Support\Facades\Auth;

class ActivityTracker
{
    public function handle($request, Closure $next)
    {
        // Determine if the request is for an API or web route
        $isApiRequest = $request->is('api/*');

        // Collect activity data
        $user = $isApiRequest ? Auth::guard('api')->user() : Auth::user();
        $ipAddress = $request->header('X-Forwarded-For', $request->ip());
        $userAgent = $request->header('X-User-Agent', $request->userAgent());
        $userLocation = file_get_contents('https://ipapi.co/'.$ipAddress.'/json/');
        $location = json_decode($userLocation);
        // Collect activity data
        $activityData = [
            'user_id' => $user ? $user->id : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'country' => isset($location['country_name']) ? $location['country_name'] : null,
            'city' => isset($location['city']) ? $location['city'] : null,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'created_at' => now(),
            'updated_at' => now(),
        ];


        // Dispatch the LogActivity job
        LogActivity::dispatch($activityData);

        return $next($request);
    }
}
