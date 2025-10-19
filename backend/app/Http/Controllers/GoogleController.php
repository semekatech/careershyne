<?php
namespace App\Http\Controllers;

use Google\Client as GoogleClient;
use Illuminate\Http\Request;

class GoogleController extends Controller
{public function redirectToGoogle(Request $request)
    {
    $userId = $request->query('user_id');

    if (! $userId) {
        return response()->json(['error' => 'User ID missing'], 400);
    }

    $client = new GoogleClient();
    $client->setClientId(env('GMAIL_CLIENT_ID'));
    $client->setClientSecret(env('GMAIL_CLIENT_SECRET'));
    $client->setRedirectUri(env('GMAIL_REDIRECT_URI'));
    $client->addScope('https://www.googleapis.com/auth/gmail.readonly');
    $client->addScope('https://www.googleapis.com/auth/gmail.send');
    $client->setAccessType('offline');
    $client->setPrompt('consent');

    // Add a signed state parameter
    $state = base64_encode(json_encode([
        'user_id' => $userId,
        'hash'    => hash_hmac('sha256', $userId, env('APP_KEY')),
    ]));

    $client->setState($state);

    return redirect($client->createAuthUrl());
}

    public function handleGoogleCallback(Request $request)
{
    $client = new GoogleClient();
    $client->setClientId(env('GMAIL_CLIENT_ID'));
    $client->setClientSecret(env('GMAIL_CLIENT_SECRET'));
    $client->setRedirectUri(env('GMAIL_REDIRECT_URI'));

    if (!$request->has('code') || !$request->has('state')) {
        return redirect('/dashboard')->with('error', 'Invalid callback data');
    }

    // Decode state
    $state = json_decode(base64_decode($request->get('state')), true);
    $userId = $state['user_id'] ?? null;
    $hash = $state['hash'] ?? '';

    // Validate signature
    if (!$userId || $hash !== hash_hmac('sha256', $userId, env('APP_KEY'))) {
        return redirect('/dashboard')->with('error', 'Invalid state');
    }

    $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

    if (isset($token['error'])) {
        return redirect('/dashboard')->with('error', 'Failed to connect Gmail');
    }

    // Retrieve user and save tokens
    $user = \App\Models\User::find($userId);

    if (!$user) {
        return redirect('/dashboard')->with('error', 'User not found');
    }

    $user->update([
        'gmail_access_token'   => $token['access_token'] ?? null,
        'gmail_refresh_token'  => $token['refresh_token'] ?? $user->gmail_refresh_token,
        'gmail_token_expires_at' => now()->addSeconds($token['expires_in'] ?? 3600),
    ]);

    return redirect('/dashboard')->with('success', 'Gmail connected successfully!');
}

}
