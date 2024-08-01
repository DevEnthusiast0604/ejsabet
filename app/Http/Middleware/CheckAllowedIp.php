<?php

namespace App\Http\Middleware;

use App\Models\CompanyIp;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CheckAllowedIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the client IP address
        $clientIp = $request->ip();

        $exempted_routes = [
            'api/login',
            'api/2fa',
            'api/search_news',
            'api/setting/get',
            'api/user',
            'api/logout',
            'api/get_client_ip',
        ];

        $current_route = $request->path();

        if (in_array($current_route, $exempted_routes)) {
            return $next($request);
        }

        // Check if the client IP is in the allowed IPs
        if (!CompanyIp::where('ip_address', $clientIp)->exists() && $request->ajax()) {
            return response()->json(['error' => 'Invalid ip address'], Response::HTTP_FORBIDDEN);
        }

        // If the IP is allowed, proceed with the request
        return $next($request);
    }
}