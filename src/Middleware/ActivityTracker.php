<?php

namespace Nrm\ActivityTracker\Middleware;

use App\Models\News;
use Closure;
use Illuminate\Support\Facades\Http;
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
        $location = $this->getIpGeolocation($ipAddress);


        // Collect activity data
        $activityData = [
            'user_id' => $user ? $user->id : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'country' => $location->country_name ?? null,
            'city' => $location->city ?? null,
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
                if (isset($newsId->id) && $newsId->id != null) {
                    return $newsId->id;
                }
            }
        }
        return null;
    }

    public function getIpGeolocation($ip)
    {
        // API key and endpoint
        $apiKey = 'd763f02c198c40aaa61fa808dd90afcd';
        $url = "https://api.ipgeolocation.io/ipgeo";

        // Send the GET request
        $response = Http::get($url, [
            'apiKey' => $apiKey,
            'ip' => $ip,
        ]);

        if ($response->successful()) {

            // Decode the JSON response
            $data = $response->json();
            return $data;
        } else {
            return response()->json(['error' => 'Unable to fetch data'], 403);
        }
    }

}
