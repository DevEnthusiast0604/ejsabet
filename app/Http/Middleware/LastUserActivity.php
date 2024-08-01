<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

class LastUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if (Auth::check()) {
        //     $expiresAt = Carbon::now()->addMinutes(1);
        //     Cache::put('user-is-online-' . Auth::user()->id, true, $expiresAt);
        // }
        $route = Route::getRoutes()->match($request);
        $isActivatePath = isset($route->parameters()['path']) && $route->parameters()['path'] === 'activate';
        $currentDomain = $request->getHttpHost();
        if ((Setting::get('site_status') === 'disabled' || (Setting::get('site_status') === 'inactive' && config('app.audit_domain') === $currentDomain))
                 && !$isActivatePath
                 && $route->uri != 'api/site_status/enable') {
            return response(view('errors.offline'), 406);
        }
        return $next($request);
    }
}