<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckKasubbagRole
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::user()?->hasRole('kasubbag_kepegawaian')) {
            abort(403);
        }

        return $next($request);
    }
}
