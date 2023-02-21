<?php

namespace App\Custom;

use App\Mail\ProguideStudentConnectMail;
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
        $subject = "User Reset Mail";
        $message = "Below is the link for your password reset. \n";
        $message .= "Please note that if you didn't request for a password reset, you should disregard this mail";
        $url = env('APP_URL') . '/reset-password?email=' . $email . '&token=' . $otp;

        Mail::to($email)->send(new UserResetPasswordMail($subject, $message, $url));
    }

    public static function SendNotificationMailToProguide($student, $proguide_email)
    {
        $subject = " New Student Connection ";
        $message = " A new student: {$student} just connected with you";

        Mail::to($proguide_email)->send(new ProguideStudentConnectMail($subject, $message));
    }

    public function PaymentNotificationMail($user_email, $user_full_name, $planDuration, $plan, $plan_amount, $reference)
    {
        $subject = "Payment Received Notification - Invoice {$reference}";
        $message = "Dear {$user_full_name}, \n";
        $message .= "I hope this email finds you well. I am writing to confirm that we have received your payment for reference number {$reference}.
         The details of your payment are as follows:";
        $message .= "Plan: {$plan}";
        $message .= "Amount: {$plan_amount}";
        $message .= "Duration: {$planDuration}";

        $message .= "Thank you ";
    }

}
