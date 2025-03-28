<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MultiAuthMiddleware
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
        // Jika user login sebagai admin
        if (Auth::guard('user')->check()) {
            return $next($request); // Izinkan akses
        }

        // Jika user login sebagai karu
        if (Auth::guard('karu')->check()) {
            return $next($request); // Izinkan akses
        }

        // Jika tidak login sebagai admin atau karu, redirect ke login
        return redirect()->route('login');
    }
}
