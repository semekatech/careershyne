<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        // --- PREPEND /api TO THE REQUEST PATH ---
        if ($request->is('api/*') && !$request->is('api/api/*')) {
            // Rewrite the request path
            $request->server->set('REQUEST_URI', '/api' . $request->getRequestUri());
        }

        $response = $next($request);

        // --- LOG ACTIVITY ---
        try {
            DB::table('activity_logs')->insert([
                'user_id' => auth()->check() ? auth()->id() : null,
                'ip' => $request->ip(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->header('User-Agent'),
                'payload' => json_encode($request->all()),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to log activity: ' . $e->getMessage());
        }

        return $response;
    }
}
