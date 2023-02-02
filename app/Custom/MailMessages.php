<?php

namespace App\Custom;

use App\Mail\UserResetPasswordMail;
use App\Mail\UserVerificationMail;
use Illuminate\Support\Facades\Mail;

class MailMessages
{

    public static function UserVerificationMail($otp, $email)
    {
        $subject = "Email Verification Notification";
        $message = "Below is the OTP for account and email verification";

        Mail::to($email)->send(new UserVerificationMail($subject, $message, $otp));
    }

    public static function UserResetPasswordMail($otp, $email)
    {
        $subject = "user Reset Mail";
        $message = "Below is the link for your password reset. \n";
        $message .= "Please note that if you didn't request for a password reset, you should disregard this mail";
        $url = env('APP_URL') . '/reset-password/' . $email . '/' . $otp;

        Mail::to($email)->send(new UserResetPasswordMail($subject, $message));
    }

}
