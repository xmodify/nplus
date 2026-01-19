<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        if (Auth::user()->role !== 'admin') {
            abort(403, 'Admin only');
        }

        if (Auth::user()->active !== 'Y') {
            abort(403, 'User inactive');
        }

        return $next($request);
    }
}
