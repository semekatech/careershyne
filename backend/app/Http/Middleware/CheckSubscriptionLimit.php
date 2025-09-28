<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimit
{

   public function handle(Request $request, Closure $next, ?string $type = null): Response
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    // Fetch subscription
    $subscription = DB::table('subscriptions')->where('user_id', $user->id)->first();
    if (!$subscription) {
        return response()->json(['success' => false, 'message' => 'No subscription found'], 403);
    }

    // Determine the limit field
    $type = $type ?? $request->route()->getName();
    $limitField = match (strtolower($type)) {
        'cv' => 'cv',
        'emails' => 'emails',
        'coverletters' => 'coverLetters',
        'checks' => 'checks',
        default => null,
    };

    // Check subscription field limit
    if (!$limitField || $subscription->$limitField <= 0) {
        return response()->json([
            'success' => false,
            'message' => "You have reached your limit for {$type}. Please upgrade your subscription plan."
        ], 403);
    }

    // Check total token usage
    $totalTokens = DB::table('usage_activities')
        ->where('user_id', $user->id)
        ->sum('tokens_used');

    if ($totalTokens >= 1000) {
        return response()->json([
            'success' => false,
            'message' => 'Token limit exceeded. Please upgrade your subscription plan.'
        ], 403);
    }

    return $next($request);
}
}
