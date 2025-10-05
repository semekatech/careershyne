<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimit
{
    public function handle(Request $request, Closure $next, ?string $type = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // 🔒 Anti-bot protection: track requests per user & IP
        $ipKey = 'usage_attempts:ip:' . $request->ip();
        $userKey = 'usage_attempts:user:' . $user->id;

        // if IP exceeds 60 requests per minute → block temporarily
        if (Cache::get($ipKey, 0) >= 15) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests from this IP. Please slow down.'
            ], 429);
        }

        // if user exceeds 30 requests per minute → block temporarily
        if (Cache::get($userKey, 0) >=2) {
            return response()->json([
                'success' => false,
                'message' => 'You are sending too many requests. Please wait a moment.'
            ], 429);
        }

        // increment counters
        Cache::add($ipKey, 0, now()->addMinute());
        Cache::add($userKey, 0, now()->addMinute());
        Cache::increment($ipKey);
        Cache::increment($userKey);

        // 🧾 Fetch subscription
        $subscription = DB::table('subscriptions')->where('user_id', $user->id)->first();
        if (!$subscription) {
            return response()->json(['success' => false, 'message' => 'No subscription found'], 403);
        }

        // Determine which field to check
        $type = $type ?? $request->route()->getName();
        $limitField = match (strtolower($type)) {
            'cv' => 'cv',
            'emails' => 'emails',
            'coverletters' => 'coverLetters',
            'checks' => 'checks',
            default => null,
        };

        // ⚙️ Check subscription limit
        if (!$limitField || $subscription->$limitField <= 0) {
            return response()->json([
                'success' => false,
                'message' => "You have reached your limit for {$type}. Please upgrade your plan."
            ], 403);
        }

        // 🧠 Check total token usage
        $totalTokens = DB::table('usage_activities')
            ->where('user_id', $user->id)
            ->sum('tokens_used');

        if ($totalTokens >= 20000) {
            return response()->json([
                'success' => false,
                'message' => 'Token limit exceeded. Please try again later.'
            ], 403);
        }

        return $next($request);
    }
}
