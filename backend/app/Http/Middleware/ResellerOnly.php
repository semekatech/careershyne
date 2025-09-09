<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResellerOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || $user->type !== 'reseller') {
            abort(403, 'Unauthorized: Reseller access only.');
        }
        return $next($request);
    }
}
