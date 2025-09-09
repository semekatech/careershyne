<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\LogActivity as ModelsLogActivity;

class LogActivity
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
        $response = $next($request);
        if (auth()->user() && ! request()->ajax()) {
            ModelsLogActivity::create([
                'url' => request()->fullUrl(),
                'ip' => request()->ip(),
                'user_id' => auth()->check() ? auth()->user()->id : 1,
                'client_id' => auth()->check() ? auth()->user()->client_id : 1,
                'method' => request()->method(),
                'agent' => request()->header('user-agent'),
            ]);
        }

        return $response;
    }
}
