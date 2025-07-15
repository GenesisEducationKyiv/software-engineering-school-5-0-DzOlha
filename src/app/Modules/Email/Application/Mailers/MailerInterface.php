<?php

namespace App\Application\Subscription\Emails\Mailers;

use Illuminate\Mail\Mailable;

interface MailerInterface
{
    public function send(string $email, Mailable $mailable): bool;
}
