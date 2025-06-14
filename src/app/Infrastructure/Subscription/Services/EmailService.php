<?php

namespace App\Infrastructure\Subscription\Services;

use App\Application\Mail\MailerInterface;
use App\Application\Subscription\Services\EmailServiceInterface;
use App\Application\Subscription\Utils\SubscriptionUrlBuilderInterface;
use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Weather\ValueObjects\WeatherData;
use App\Infrastructure\Subscription\Mails\Confirmation\ConfirmationMail;
use App\Infrastructure\Subscription\Mails\Confirmation\ConfirmationMailData;
use App\Infrastructure\Subscription\Mails\Update\WeatherUpdateMail;
use App\Infrastructure\Subscription\Mails\Update\WeatherUpdateMailData;

class EmailService implements EmailServiceInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private SubscriptionUrlBuilderInterface $urlBuilder
    ) {
    }

    public function sendConfirmationEmail(Subscription $subscription): bool
    {
        $email = $subscription->getEmail()->getValue();
        $confirmUrl = $this->urlBuilder->buildConfirmationUrl($subscription);

        if (!$confirmUrl) {
            return false;
        }

        $mailData = new ConfirmationMailData($confirmUrl, $subscription);

        return $this->mailer->send($email, new ConfirmationMail($mailData));
    }

    public function sendWeatherUpdate(Subscription $subscription, WeatherData $weatherData): bool
    {
        $email = $subscription->getEmail()->getValue();
        $unsubscribeUrl = $this->urlBuilder->buildUnsubscribeUrl($subscription);

        if (!$unsubscribeUrl) {
            return false;
        }

        $mailData = new WeatherUpdateMailData(
            $subscription->getCity()->getName(),
            $subscription->getFrequency()->getName(),
            $weatherData,
            $unsubscribeUrl
        );

        return $this->mailer->send($email, new WeatherUpdateMail($mailData));
    }
}
