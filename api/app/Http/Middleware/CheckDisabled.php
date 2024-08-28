<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDisabled
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
        if(auth()->check() && auth()->user()->disabled_at){
            return response([
                'disabled' => true,
                'message' => 'Whoops! It appears that your account has been disabled. If you think this is a mistake, please contact us for assistance.',
            ], 401);
        }

        return $next($request);
    }
}
