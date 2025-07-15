<?php

namespace App\Modules\Email\Application\Mailers;

use Illuminate\Mail\Mailable;

interface MailerInterface
{
    public function send(string $email, Mailable $mailable): bool;
}
