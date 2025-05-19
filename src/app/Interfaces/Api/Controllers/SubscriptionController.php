<?php

namespace App\Interfaces\Api\Controllers;

use App\Application\Subscription\Commands\ConfirmSubscriptionCommand;
use App\Application\Subscription\Commands\CreateSubscriptionCommand;
use App\Application\Subscription\Commands\UnsubscribeCommand;
use App\Application\Subscription\DTOs\ConfirmSubscriptionRequestDTO;
use App\Application\Subscription\DTOs\CreateSubscriptionRequestDTO;
use App\Application\Subscription\DTOs\UnsubscribeRequestDTO;
use App\Domain\Subscription\ValueObjects\Token;
use App\Exceptions\Custom\CityNotFoundException;
use App\Exceptions\Custom\EmailAlreadySubscribedException;
use App\Exceptions\Custom\SubscriptionAlreadyPendingException;
use App\Exceptions\Custom\TokenNotFoundException;
use App\Exceptions\ValidationException;
use App\Interfaces\Api\Requests\SubscribeRequest;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly CreateSubscriptionCommand  $createSubscriptionCommand,
        private readonly ConfirmSubscriptionCommand $confirmSubscriptionCommand,
        private readonly UnsubscribeCommand         $unsubscribeCommand
    ){}

    /**
     * POST /subscribe
     * @param SubscribeRequest $request
     * @return JsonResponse
     */
    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        $dto = new CreateSubscriptionRequestDTO(
            $request->email,
            $request->city,
            $request->frequency
        );

        try {
            $this->createSubscriptionCommand->execute($dto);
        } catch (ValidationException|CityNotFoundException|EmailAlreadySubscribedException|SubscriptionAlreadyPendingException $e) {
            return $this->errorResponse($e);
        }

        return $this->successResponse(
            'Subscription successful. Confirmation email sent. Please check your inbox.'
        );
    }

    /**
     * GET /confirm/{token}
     * @param string $token
     * @return JsonResponse
     */
    public function confirm(string $token): JsonResponse
    {
        try {
            $dto = new ConfirmSubscriptionRequestDTO(
                Token::confirmation($token)
            );
            $this->confirmSubscriptionCommand->execute($dto);
        } catch (ValidationException|TokenNotFoundException $e) {
            return $this->errorResponse($e);
        }

        return $this->successResponse(
            'Subscription confirmed. You will now receive weather updates.'
        );
    }

    /**
     * GET /unsubscribe/{token}
     * @param string $token
     * @return JsonResponse
     */
    public function unsubscribe(string $token): JsonResponse
    {
        try {
            $dto = new UnsubscribeRequestDTO(
                Token::cancel($token)
            );

            $this->unsubscribeCommand->execute($dto);
        } catch (ValidationException|TokenNotFoundException $e) {
            return $this->errorResponse($e);
        }

        return $this->successResponse(
            'You have been unsubscribed from weather updates.'
        );
    }
}
