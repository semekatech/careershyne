<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Campaign;
use App\Models\Subscription;
use Illuminate\Support\Facades\Storage;
use App\Mail\BidSubmittedMail;
use App\Mail\BidStatusNotification;
use Illuminate\Support\Facades\Mail;
use DB;

class BidController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->first();
        $campaigns = Campaign::where('user_id', $user->id)->get();
        return response()->json(['campaigns' => $campaigns], 200);
    }
    public function getBids(Request $request)
    {
        $user = User::where('api_token', hash('sha256', $request->bearerToken()))->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $role = $user->role;

        $bids = DB::table('bids')
            ->join('campaigns', 'bids.campaign_id', '=', 'campaigns.id')
            ->join('users', 'bids.user_id', '=', 'users.id')
            ->when($role === 'brand', function ($query) use ($user) {
                $campaigns = Campaign::where('user_id', $user->id)->pluck('id')->toArray();
                $query->whereIn('bids.campaign_id', $campaigns);
            })
            ->when($role === 'promoter', function ($query) use ($user) {
                $query->where('bids.user_id', $user->id);
            })
            ->orderBy('bids.created_at', 'desc')
            ->select(
                'bids.id',
                'bids.amount',
                'bids.note',
                'bids.status',
                'bids.created_at',
                'campaigns.id as campaign_id',
                'campaigns.title as campaign_title',
                'campaigns.brand as campaign_brand',
                'users.name as promoter_name',
                'users.email as promoter_email',
                'users.phone as promoter_phone',
                'users.instagram',
                'users.twitter',
                'users.facebook',
                'users.photo as promoter_photo',
                'users.youtube',
                'users.tiktok',
                'users.niche',
                'users.bio',
                'users.location',
                'users.dob',
            )
            ->get();

        return response()->json(['bids' => $bids]);
    }

    public function approve(Request $request, $id)
    {
        $user = User::where('api_token', hash('sha256', $request->bearerToken()))->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $bid = Bid::findOrFail($id);
        $campaign = Campaign::findOrFail($bid->campaign_id);
        $bidder = User::findOrFail($bid->user_id);
        if ($campaign->user_id != $user->id) {
            return response()->json(['message' => 'Forbidden: You do not own this campaign'], 403);
        }

        $bid->status = 'approved';
        $bid->save();

        $bid->campaign_title = $campaign->title;
        $bid->status = 'approved';
        $bid->user_name = $bidder->name;
        try {
            Mail::to($bidder->email)->send(new BidStatusNotification($bid));
            info('sent');
        } catch (\Exception $e) {
            // Handle the exception - log it, notify admin, etc.
            info('Failed to send bid status notification email: ' . $e->getMessage());

            // Optionally, you can throw again or return false or do something else
        }
        return response()->json([
            'message' => 'Bid approved successfully',
            'bid' => $bid
        ]);
    }

    public function reject(Request $request, $id)
    {
        $user = User::where('api_token', hash('sha256', $request->bearerToken()))->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $bid = Bid::findOrFail($id);
        $campaign = Campaign::findOrFail($bid->campaign_id);
        $bidder = User::findOrFail($bid->user_id);
        if ($campaign->user_id != $user->id) {
            return response()->json(['message' => 'Forbidden: You do not own this campaign'], 403);
        }

        $bid->status = 'rejected';
        $bid->save();

        $bid->campaign_title = $campaign->title;
        $bid->status = 'rejected';
        $bid->user_name = $bidder->name;
        try {
            Mail::to($bidder->email)->send(new BidStatusNotification($bid));
            info('sent');
        } catch (\Exception $e) {
            // Handle the exception - log it, notify admin, etc.
            info('Failed to send bid status notification email: ' . $e->getMessage());

            // Optionally, you can throw again or return false or do something else
        }

        return response()->json([
            'message' => 'Bid rejected successfully',
            'bid' => $bid
        ]);
    }




    public function submit(Request $request)
    {
        $user = User::where('api_token', hash('sha256', $request->bearerToken()))->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
        $subscription = Subscription::where('user_id', $user->id)->first();
        if (!$subscription || $subscription->limit <= 0) {
            return response()->json([
                'message' => 'You have reached your bidding limit. Please upgrade your plan.'
            ], 403);
        }
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:1000',
        ]);
        $existingBid = Bid::where('user_id', $user->id)
            ->where('campaign_id', $request->campaign_id)
            ->first();
        if ($existingBid) {
            return response()->json([
                'message' => 'You have already submitted a bid for this campaign.',
            ], 409);
        }
        $campaign = Campaign::findOrFail($request->campaign_id);
        $bid = new Bid();
        $bid->user_id = $user->id;
        $bid->campaign_id = $request->campaign_id;
        $bid->amount = $request->amount;
        $bid->note = $request->note;
        $bid->save();
        $bid->campaign_title = $campaign->title;
        $bid->company = $campaign->brand;
        $bid->by = $user->name;
        $subscription->limit = max(0, $subscription->limit - 1);
        $subscription->save();
        try {
            Mail::to($user->email)->queue(new BidSubmittedMail($bid, $user, $campaign));
            info('bid submitted and sent email:');
        } catch (\Exception $e) {
            info('Failed to send bid submission email: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Bid submitted successfully.']);
    }




    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $bid = Bid::find($id);
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['valid' => false, 'message' => 'Authorization header missing or invalid'], 401);
        }

        $plainToken = substr($authHeader, 7);
        $hashedToken = hash('sha256', $plainToken);
        $user = User::where('api_token', $hashedToken)->first();

        if (!$user) {
            return response()->json(['valid' => false, 'message' => 'UnAuthorized Access'], 403);
        }

        if (!$bid) {
            return response()->json(['message' => 'Bid not found'], 404);
        }
        $bid->amount = $request->input('amount');
        $bid->note = $request->input('note');
        $bid->save();

        return response()->json([
            'message' => 'Bid updated successfully',
            'bid' => $bid,
        ]);
    }
    public function verifyToken(Request $request)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['valid' => false, 'message' => 'Authorization header missing or invalid'], 401);
        }

        $plainToken = substr($authHeader, 7);
        $hashedToken = hash('sha256', $plainToken);

        $user = User::where('api_token', $hashedToken)->first();

        if (!$user) {
            return response()->json(['valid' => false, 'message' => 'Invalid token'], 401);
        }

        return response()->json([
            'valid' => true,
            'user' => $user,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $campaign = Campaign::findOrFail($id);
        return response()->json($campaign);
    }

    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */ public function fetchAll()
    {
        return Campaign::where('status', 1)->orderByDesc('id')->get();
    }
}
