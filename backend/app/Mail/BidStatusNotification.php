<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class BidStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $bid;

    public function __construct($bid)
    {
        $this->bid = $bid;
    }

    public function build()
    {
        return $this->subject('Promotion Request Status Notification!')
            ->view('emails.bid_status_notification');
    }
}
