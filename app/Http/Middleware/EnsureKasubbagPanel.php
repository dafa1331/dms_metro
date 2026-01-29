<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureKasubbagPanel
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check() || ! auth()->user()->hasRole('kasubbag_kepegawaian')) {
            abort(403);
        }

        return $next($request);
    }
}
