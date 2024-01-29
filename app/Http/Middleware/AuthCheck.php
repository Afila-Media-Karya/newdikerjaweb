<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class AuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('administrator')->check() && !Auth::check()) {
            return $next($request);
        } else {
            if (Auth::guard('administrator')->check()) {
                return redirect()->route('dashboard.admin');
            } else if (Auth::check()) {
                $role = Auth::user()->role;
                if ($role == 1)
                    return redirect()->route('dashboard.pegawai');
                else if ($role == 2)
                    return redirect()->route('dashboard.admin_opd');
            }
        }
        return back();
    }
}
