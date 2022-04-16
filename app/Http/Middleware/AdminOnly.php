<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_admin)
        {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Forbidden.'], 403);
        }

        return redirect('/');
    }
}
