<?php

namespace App\Http\Middleware;

use App\Models\SenderName;
use Closure;
use Illuminate\Http\Request;
use Session;

class CheckSenderName
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
        $clientID = auth()->user()->client_id;
        $senderID = SenderName::whereclient_id($clientID)->first();
        if ($clientID == null) {
            return redirect()->route('clients.create')->with('success','Kindly Register Your Company details to Continue');
        }elseif($senderID == null){
            return redirect()->route('sendernames.application.apply')->with('success','Kindly Apply for Your Company SenderName to Continue');
        }else{
            Session::put('client_has_sendername', auth()->user()->id);
        }
        return $next($request);
    }
}
