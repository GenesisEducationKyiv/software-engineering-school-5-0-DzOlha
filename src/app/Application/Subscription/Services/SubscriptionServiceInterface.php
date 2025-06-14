<?php

namespace App\Application\Subscription\Services;

use App\Application\Subscription\DTOs\ConfirmSubscriptionRequestDTO;
use App\Application\Subscription\DTOs\UnsubscribeRequestDTO;
use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Subscription\ValueObjects\Email\Email;
use App\Domain\Subscription\ValueObjects\Frequency\Frequency;
use App\Domain\Weather\ValueObjects\City\City;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Exceptions\Custom\EmailAlreadySubscribedException;
use App\Exceptions\Custom\FrequencyNotFoundException;
use App\Exceptions\Custom\SubscriptionAlreadyPendingException;
use App\Exceptions\Custom\TokenNotFoundException;

interface SubscriptionServiceInterface
{
    /**
     * @throws CityNotFoundException
     * @throws EmailAlreadySubscribedException
     * @throws SubscriptionAlreadyPendingException
     * @throws ApiAccessException
     * @throws FrequencyNotFoundException
     */
    public function subscribe(Email $email, City $city, Frequency $frequency): Subscription;

    /**
     * @throws TokenNotFoundException
     */
    public function confirmSubscription(ConfirmSubscriptionRequestDTO $dto): ?Subscription;

    /**
     * @throws TokenNotFoundException
     */
    public function unsubscribe(UnsubscribeRequestDTO $dto): ?bool;
}
