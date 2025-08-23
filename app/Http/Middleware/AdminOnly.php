<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
namespace App\Http\Middleware;


class AdminOnly
{
     /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('get-login')->with('failed', 'Please login to access this page.');
        }

        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->with('failed', 'Admin access required.');
        }

        return $next($request);
    }
}
