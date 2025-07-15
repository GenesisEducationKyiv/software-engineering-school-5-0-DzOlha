<?php

namespace App\Modules\Email\Application;

use App\Modules\Email\Domain\Entities\EmailSubscriptionEntity;
use App\Modules\Email\Domain\Entities\EmailWeatherEntity;

interface EmailServiceInterface
{
    public function sendConfirmationEmail(
        EmailSubscriptionEntity $subscription
    ): bool;

    public function sendWeatherUpdate(
        EmailSubscriptionEntity $subscription,
        EmailWeatherEntity $weatherData
    ): bool;
}
