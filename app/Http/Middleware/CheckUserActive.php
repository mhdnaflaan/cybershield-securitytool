<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserActive
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->is_active) {
            auth()->logout();
            return redirect('/login')->withErrors(['email' => 'Your account has been blocked. Contact admin.']);
        }

        return $next($request);
    }
}