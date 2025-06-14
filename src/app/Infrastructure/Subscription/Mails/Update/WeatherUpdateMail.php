<?php

namespace App\Infrastructure\Subscription\Mails\Update;

use Illuminate\Mail\Mailable;

class WeatherUpdateMail extends Mailable
{
    public function __construct(
        private readonly WeatherUpdateMailData $data
    ) {
    }

    public function build(): Mailable
    {
        return $this->subject("Weather update for {$this->data->getCity()}")
            ->view('emails.weather-update')
            ->with(['data' => $this->data]);
    }
}
