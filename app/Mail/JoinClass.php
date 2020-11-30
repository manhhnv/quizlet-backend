<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JoinClass extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $from_address = '';
    public $to_address = '';
    public $confirm_link = '';
    public function __construct($from=null, $to=null, $confirm=null)
    {
        $this->from_address = $from;
        $this->to_address = $to;
        $this->confirm_link = $confirm;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->from_address, 'Quizlet Request Join Class')
            ->subject('Quizlet Request Join Class')
            ->markdown('email.request')
            ->with([
                'from' => $this->from_address,
                'to' => $this->to_address,
                'link' => $this->confirm_link
            ]);
    }
}
