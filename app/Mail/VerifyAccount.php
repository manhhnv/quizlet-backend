<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyAccount extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user_mail = '';
    public $verify_link = '';
    public function __construct($mail=null, $link=null)
    {
        $this->user_mail = $mail;
        $this->verify_link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('manh117bg@gmail.com', 'Quizlet JP Team')
            ->subject('Verify Quizlet JP')
            ->markdown('email.verify_account')
            ->with([
                'name' => $this->user_mail ,
                'link' => 'http://localhost:9000/api/auth' . $this->verify_link
            ]);
    }
}
