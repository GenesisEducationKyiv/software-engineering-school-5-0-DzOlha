<?php

namespace App\Application\Subscription\Emails\Services;

use App\Application\Subscription\Emails\Mails\Confirmation\ConfirmationMail;
use App\Application\Subscription\Emails\Mails\Confirmation\ConfirmationMailData;
use App\Application\Subscription\Emails\Mails\Update\WeatherUpdateMail;
use App\Application\Subscription\Emails\Mails\Update\WeatherUpdateMailData;
use App\Application\Subscription\Emails\EmailServiceInterface;
use App\Application\Subscription\Emails\Mailers\MailerInterface;
use App\Application\Subscription\Utils\Builders\SubscriptionLinkBuilderInterface;
use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Weather\ValueObjects\WeatherData;
use App\Infrastructure\Subscription\Utils\Links\extend\ConfirmationLink;
use App\Infrastructure\Subscription\Utils\Links\extend\UnsubscribeLink;

class EmailService implements EmailServiceInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly SubscriptionLinkBuilderInterface $urlBuilder
    ) {
    }

    public function sendConfirmationEmail(Subscription $subscription): bool
    {
        $email = $subscription->getEmail()->getValue();
        $confirmUrl = $this->urlBuilder->build(
            new ConfirmationLink($subscription)
        );

        if (!$confirmUrl) {
            return false;
        }

        $mailData = new ConfirmationMailData($confirmUrl, $subscription);

        return $this->mailer->send($email, new ConfirmationMail($mailData));
    }

    public function sendWeatherUpdate(Subscription $subscription, WeatherData $weatherData): bool
    {
        $email = $subscription->getEmail()->getValue();
        $unsubscribeUrl = $this->urlBuilder->build(
            new UnsubscribeLink($subscription)
        );

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
