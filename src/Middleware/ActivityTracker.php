<?php

namespace Nrm\ActivityTracker\Middleware;

use App\Models\News;
use Closure;
use Nrm\ActivityTracker\Jobs\LogActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
        $newsId = $this->getNewsId($request);
        $cacheKey = 'user_location_' . $ipAddress;
        $userLocation = Cache::get($cacheKey);
        if (!$userLocation) {
            $userLocation = file_get_contents("https://api.ipgeolocation.io/ipgeo?apiKey=b4c08caf46b0479f838286d517af5d09&ip=$ipAddress");
            Cache::put($cacheKey, $userLocation);
        }
        $location = json_decode($userLocation);
        // Collect activity data
        $activityData = [
            'user_id' => $user ? $user->id : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'country' => isset($location->country_name) ? $location->country_name : null,
            'city' => isset($location->city) ? $location->city : null,
            'news_id' => $newsId,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'created_at' => now(),
            'updated_at' => now(),
        ];


        // Dispatch the LogActivity job
        LogActivity::dispatch($activityData);

        return $next($request);
    }

    function getNewsId($request)
    {
        $segments = $request->segments();

        if (count($segments) >= 2 && $segments[count($segments) - 2] === 'news') {
            $lastSegment = $segments[count($segments) - 1];
            if ($lastSegment != null) {
                $newsId = News::where('permalink', $lastSegment)->first();
                if(isset($newsId->id) && $newsId->id !=null){
                    return $newsId->id;
                }
            }
        }
        return null;
    }
}
