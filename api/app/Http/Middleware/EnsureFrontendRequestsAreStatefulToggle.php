<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class EnsureFrontendRequestsAreStatefulToggle
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
        if(!(bool)env('APP_AUTH_TOKEN', false)){
            $handler = new EnsureFrontendRequestsAreStateful();
            return $handler->handle($request,  $next);
        }
        return $next($request);
    }
}
