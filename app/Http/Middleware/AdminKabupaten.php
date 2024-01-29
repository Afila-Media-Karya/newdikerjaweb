<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
class AdminKabupaten
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('administrator')->check()) {
            
            if (Auth::guard('administrator')->user()->role == '2') {
                return $next($request);
            }else{
                Auth::guard('administrator')->logout();
                return redirect('login');
            }
        } else {
            Auth::guard('administrator')->logout();
            return redirect()->route('login');
        }

        return redirect()->back();



    }
}
