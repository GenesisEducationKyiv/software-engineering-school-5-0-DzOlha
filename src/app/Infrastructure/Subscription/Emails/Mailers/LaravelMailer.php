<?php

namespace App\Infrastructure\Subscription\Emails\Mailers;

use App\Application\Subscription\Emails\Mailers\MailerInterface;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LaravelMailer implements MailerInterface
{
    /**
     * @param string $email
     * @param Mailable $mailable
     * @return bool
     */
    public function send(string $email, Mailable $mailable): bool
    {
        try {
            Mail::to($email)->send($mailable);
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send email to {$email}: " . $e->getMessage());
            return false;
        }
    }
}
