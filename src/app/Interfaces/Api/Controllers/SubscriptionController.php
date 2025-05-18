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
use App\Interfaces\Api\Requests\SubscribeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly CreateSubscriptionCommand $createSubscriptionCommand,
        private readonly ConfirmSubscriptionCommand $confirmSubscriptionCommand,
        private readonly UnsubscribeCommand $unsubscribeCommand
    ) {
    }

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
        }
        catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
        catch (
            CityNotFoundException |
            EmailAlreadySubscribedException |
            SubscriptionAlreadyPendingException $e
        ) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscription successful. Confirmation email sent.',
        ], Response::HTTP_OK);
    }

    /**
     * GET /confirm/{token}
     * @param string $token
     * @return JsonResponse
     */
    public function confirm(string $token): JsonResponse
    {
        $dto = new ConfirmSubscriptionRequestDTO(
            Token::confirmation($token)
        );

        try {
            $this->confirmSubscriptionCommand->execute($dto);
        }
        catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
        catch (TokenNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscription confirmed successfully.',
        ]);
    }

    /**
     * GET /unsubscribe/{token}
     * @param string $token
     * @return JsonResponse
     */
    public function unsubscribe(string $token): JsonResponse
    {
        $dto = new UnsubscribeRequestDTO(
            Token::cancel($token)
        );

        try {
            $this->unsubscribeCommand->execute($dto);
        }
        catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
        catch (TokenNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }

        return response()->json([
            'success' => true,
            'message' => 'Unsubscribed successfully.',
        ]);
    }
}
