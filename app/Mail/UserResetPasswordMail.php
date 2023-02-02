<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $message;
    public $subject;
    public $otp;

    public function __construct($subject, $message)
    {
        $this->message = $message;
        $this->subject = $subject;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $subject = $this->subject;
        $message = $this->message;
        $otp = $this->otp;
        return $this->markdown('mail.user-reset-password-mail', compact('subject', 'message', 'otp'));
    }
}
