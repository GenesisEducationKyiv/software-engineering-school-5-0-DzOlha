<?php

namespace App\Infrastructure\Mail;

use App\Application\Mail\MailerInterface;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LaravelMailer implements MailerInterface
{
    /**
     * @param string $to
     * @param Mailable $mailable
     * @return bool
     */
    public function send(string $to, Mailable $mailable): bool
    {
        try {
            Mail::to($to)->send($mailable);
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send email to {$to}: " . $e->getMessage());
            return false;
        }
    }
}
