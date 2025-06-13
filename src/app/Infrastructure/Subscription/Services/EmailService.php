<?php

namespace App\Infrastructure\Subscription\Services;

use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Weather\ValueObjects\WeatherData;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class EmailService
{
    private string $confirmWebEndpoint = "/confirm";
    private string $unsubscribeWebEndpoint = "/unsubscribe";

    public function sendConfirmationEmail(Subscription $subscription): void
    {
        $email = $subscription->getEmail()->getValue();
        $token = $subscription->getConfirmationToken()?->getValue();

        if (!$token) {
            Log::info('Sending confirmation email to: '
                                . $subscription->getEmail()
                                . " was FAILED due to absent confirmation token");
            return;
        }

        $confirmUrl = URL::to("{$this->confirmWebEndpoint}?token={$token}");

        Mail::to($email)->send(new class ($confirmUrl, $subscription) extends Mailable {
            public function __construct(
                private readonly string $confirmUrl,
                private readonly Subscription $subscription
            ) {
            }

            public function build(): Mailable
            {
                Log::info('Sending confirmation email to: ' . $this->subscription->getEmail());

                return $this->subject('Confirm your weather subscription')
                    ->view('emails.confirm-subscription')
                    ->with([
                        'confirmUrl' => $this->confirmUrl,
                        'subscription' => $this->subscription,
                    ]);
            }
        });
    }

    public function sendWeatherUpdate(Subscription $subscription, WeatherData $weatherData): void
    {
        $email = $subscription->getEmail()->getValue();
        $city = $subscription->getCity()->getName();
        $frequency = $subscription->getFrequency()->getName();
        $unsubscribeToken = $subscription->getUnsubscribeToken()?->getValue();

        if (!$unsubscribeToken) {
            Log::info('Sending weather updates letter to: '
                                . $subscription->getEmail()
                                . " was FAILED due to absent unsubscribe token");
            return;
        }

        $unsubscribeUrl = URL::to("{$this->unsubscribeWebEndpoint}?token={$unsubscribeToken}");

        Mail::to($email)->send(new class ($city, $frequency, $weatherData, $unsubscribeUrl) extends Mailable {
            public function __construct(
                private readonly string $city,
                private readonly string $frequency,
                private readonly WeatherData $weatherData,
                private readonly string $unsubscribeUrl
            ) {
            }

            public function build(): Mailable
            {
                Log::info('Sending weather update');

                return $this->subject("Weather update for {$this->city}")
                    ->view('emails.weather-update')
                    ->with([
                        'city' => $this->city,
                        'frequency' => $this->frequency,
                        'temperature' => $this->weatherData->getTemperature(),
                        'humidity' => $this->weatherData->getHumidity(),
                        'description' => $this->weatherData->getDescription(),
                        'unsubscribeUrl' => $this->unsubscribeUrl,
                    ]);
            }
        });
    }
}
