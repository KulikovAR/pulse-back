<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TelescopeAuthorization
{
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array(app()->environment(), ['local', 'develop'])) {
            return $next($request);
        }

        if (! auth()->check()) {
            abort(403);
        }

        return $next($request);
    }
} 