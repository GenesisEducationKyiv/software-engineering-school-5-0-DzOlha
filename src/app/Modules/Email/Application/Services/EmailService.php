<?php

namespace App\Modules\Email\Application\Services;

use App\Modules\Email\Application\EmailServiceInterface;
use App\Modules\Email\Application\Mailers\MailerInterface;
use App\Modules\Email\Application\Mails\Confirmation\ConfirmationMail;
use App\Modules\Email\Application\Mails\Confirmation\ConfirmationMailData;
use App\Modules\Email\Application\Mails\Update\WeatherUpdateMail;
use App\Modules\Email\Application\Mails\Update\WeatherUpdateMailData;
use App\Modules\Email\Application\Utils\Builders\SubscriptionLinkBuilderInterface;
use App\Modules\Email\Application\Utils\Links\Implementation\Concrete\ConfirmationLink;
use App\Modules\Email\Application\Utils\Links\Implementation\Concrete\UnsubscribeLink;
use App\Modules\Email\Domain\Entities\EmailSubscriptionEntity;
use App\Modules\Email\Domain\Entities\EmailWeatherEntity;

class EmailService implements EmailServiceInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly SubscriptionLinkBuilderInterface $urlBuilder
    ) {
    }

    public function sendConfirmationEmail(EmailSubscriptionEntity $subscription): bool
    {
        $email = $subscription->getEmail();
        $confirmUrl = $this->urlBuilder->build(
            new ConfirmationLink($subscription)
        );

        if (!$confirmUrl) {
            return false;
        }

        $mailData = new ConfirmationMailData($confirmUrl, $subscription);

        return $this->mailer->send($email, new ConfirmationMail($mailData));
    }

    public function sendWeatherUpdate(
        EmailSubscriptionEntity $subscription,
        EmailWeatherEntity $weatherData
    ): bool {
        $email = $subscription->getEmail();
        $unsubscribeUrl = $this->urlBuilder->build(
            new UnsubscribeLink($subscription)
        );

        if (!$unsubscribeUrl) {
            return false;
        }

        $mailData = new WeatherUpdateMailData(
            $subscription->getCity(),
            $subscription->getFrequency(),
            $weatherData,
            $unsubscribeUrl
        );

        return $this->mailer->send($email, new WeatherUpdateMail($mailData));
    }
}
