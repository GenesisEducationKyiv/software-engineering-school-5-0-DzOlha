<?php

namespace App\Application\Mail;

use Illuminate\Mail\Mailable;

interface MailerInterface
{
    public function send(string $to, Mailable $mailable): bool;
}
