<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $role = auth()->user()->role;
       
        // Allow access to: student, admin
        if (!in_array($role, ['student', 'admin'])) {
            abort(403, 'Access denied. Student role required.');
        }

        return $next($request);
    }
}