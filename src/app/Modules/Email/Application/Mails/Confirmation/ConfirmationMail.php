<?php

namespace App\Modules\Email\Application\Mails\Confirmation;

use Illuminate\Mail\Mailable;

class ConfirmationMail extends Mailable
{
    public function __construct(
        private readonly ConfirmationMailData $data
    ) {
    }

    public function build(): Mailable
    {
        return $this->subject('Confirm your weather subscription')
            ->view('emails.confirm-subscription')
            ->with(['data' => $this->data]);
    }
}
