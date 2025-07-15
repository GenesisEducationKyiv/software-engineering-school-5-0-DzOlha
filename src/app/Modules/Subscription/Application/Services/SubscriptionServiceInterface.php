<?php

namespace App\Modules\Subscription\Application\Services;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Exceptions\Custom\EmailAlreadySubscribedException;
use App\Exceptions\Custom\FrequencyNotFoundException;
use App\Exceptions\Custom\SubscriptionAlreadyPendingException;
use App\Exceptions\Custom\TokenNotFoundException;
use App\Modules\Subscription\Application\DTOs\ConfirmSubscriptionRequestDTO;
use App\Modules\Subscription\Application\DTOs\UnsubscribeRequestDTO;
use App\Modules\Subscription\Domain\Entities\Subscription;
use App\Modules\Subscription\Domain\ValueObjects\Email\Email;
use App\Modules\Subscription\Domain\ValueObjects\Frequency\Frequency;
use App\Modules\Subscription\Domain\ValueObjects\City\City;

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
