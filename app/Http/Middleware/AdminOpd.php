<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
class AdminOpd
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         if (Auth::check()) {
            
            if (Auth::user()->role == '1' || Auth::user()->role == '3') {
                return $next($request);
            }else{
                Auth::logout(); // Log out the user
                return redirect()->route('login');
            }
        } else {
                Auth::logout(); // Log out the user
                return redirect()->route('login');
        }

        return redirect()->back();
    }
}
