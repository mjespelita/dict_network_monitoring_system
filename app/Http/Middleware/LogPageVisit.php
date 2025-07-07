<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LogPageVisit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            DB::table('page_visits')->insert([
                'user_id' => Auth::id(),
                'url' => $request->getPathInfo(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'visited_at' => now(),
            ]);
        }

        return $next($request);
    }
}
