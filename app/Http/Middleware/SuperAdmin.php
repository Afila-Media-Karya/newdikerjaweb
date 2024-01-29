<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
class SuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('administrator')->check() && Auth::guard('administrator')->user()->role == '1') {
                return $next($request);
        }
        if (!Auth::guard('administrator')->check()) {
            return redirect('/login');
        }

        if (Auth::check()) {
           return redirect()->back();
        }
        return redirect()->back();
    }
}
