<?php

namespace App\Infrastructure\Subscription\Utils;

use App\Application\Subscription\Utils\SubscriptionUrlBuilderInterface;
use App\Domain\Subscription\Entities\Subscription;
use Illuminate\Support\Facades\URL;

class SubscriptionUrlBuilder implements SubscriptionUrlBuilderInterface
{
    public function __construct(
        private readonly string $confirmWebEndpoint = '/confirm',
        private readonly string $unsubscribeWebEndpoint = '/unsubscribe'
    ) {
    }

    public function buildConfirmationUrl(Subscription $subscription): ?string
    {
        $token = $subscription->getConfirmationToken()?->getValue();
        return $token ? URL::to("{$this->confirmWebEndpoint}?token={$token}") : null;
    }

    public function buildUnsubscribeUrl(Subscription $subscription): ?string
    {
        $token = $subscription->getUnsubscribeToken()?->getValue();
        return $token ? URL::to("{$this->unsubscribeWebEndpoint}?token={$token}") : null;
    }
}
