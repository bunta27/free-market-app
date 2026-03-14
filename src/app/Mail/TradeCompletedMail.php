<?php

namespace App\Mail;

use App\Models\Trade;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TradeCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Trade $trade;

    public function __construct(Trade $trade)
    {
        $this->trade = $trade;
    }

    public function build()
    {
        return $this->subject('取引完了のお知らせ')
            ->view('emails.trade-completed');
    }
}