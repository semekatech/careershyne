<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $type  Type of action (cv, email, coverLetters, checks)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ?string $type = null): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        // Fetch subscription usage
        $subscription = DB::table('subscriptions')->where('user_id', $user->id)->first();
        if (!$subscription) {
            return response()->json(['success' => false, 'message' => 'No subscription found'], 403);
        }
        $type = $type ?? $request->route()->getName();
        $limitField = match ($type) {
            'cv' => 'cv',
            'emails' => 'emails',
            'coverletters' => 'coverLetters',
            'checks' => 'checks',
            default => null,
        };
        if (!$limitField || $subscription->$limitField <= 0) {
            return response()->json([
                'success' => false,
                'message' => "You have reached your limit for {$type}. Please upgrade your subscription plan."
            ], 403);
        }
        return $next($request);
    }
}
