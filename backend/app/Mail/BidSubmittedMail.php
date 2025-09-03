<?php

namespace App\Mail;

use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BidSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bid;

    public $user;
    public $campaign;

    public function __construct($bid, $user, $campaign)
    {
        $this->bid = $bid;
        $this->user = $user;
        $this->campaign = $campaign;
    }


    public function build()
    {
        return $this->subject('Expression Of Interest For ' . $this->campaign->title)
            ->view('emails.bid')
            ->with([
                'bid' => $this->bid,
                'user' => $this->user,
                'campaign' => $this->campaign,
            ]);
    }
}
