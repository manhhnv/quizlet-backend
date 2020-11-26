<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShareLink extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $from_address = '';
    public $to_address = '';
    public $share_link = '';
    public function __construct($from=null, $to=null, $link=null)
    {
        $this->from_address = $from;
        $this->to_address = $to;
        $this->share_link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->from_address, 'Quizlet JP Share')
            ->subject('Quizlet JP Share')
            ->markdown('email.share')
            ->with([
                'from' => $this->from_address,
                'to' => $this->to_address,
                'link' => $this->share_link
            ]);
    }
}
