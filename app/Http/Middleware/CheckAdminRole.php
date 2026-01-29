<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        // user PASTI sudah login di sini
        if (! $request->user()->hasRole('admin')) {
            abort(403);
        }

        return $next($request);
    }
}

