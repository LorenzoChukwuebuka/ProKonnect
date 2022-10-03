<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserVerificationMail extends Mailable
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

    public function __construct($subject,$message,$otp)
    {
        $this->message = $message;
        $this->subject = $subject;
        $this->otp = $otp;
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

        return $this->markdown('mail.user-verification-mail',compact('subject','message','otp'));
    }
}
