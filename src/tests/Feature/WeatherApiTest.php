<?php

namespace Tests\Feature;

use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use App\Domain\Subscription\ValueObjects\Email;
use App\Domain\Subscription\ValueObjects\Frequency;
use App\Domain\Weather\ValueObjects\City;
use App\Exceptions\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    use RefreshDatabase;

    private SubscriptionRepositoryInterface $repository;

    // ERROR MESSAGE CONSTANTS
    private const ERROR_MESSAGES = [
        'city_not_found' => 'City not found',
        'token_not_found' => 'Token not found',
        'email_already_subscribed' => 'Email already subscribed',
        'subscription_pending' => 'A subscription with the provided details is already pending. Please check your inbox to confirm it.',
        'subscription_success' => 'Subscription successful. Confirmation email sent. Please check your inbox.',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(SubscriptionRepositoryInterface::class);
    }

    // WEATHER ENDPOINT TESTS

    #[DataProvider('weatherValidationProvider')]
    public function test_get_current_weather_validation(string $queryString, int $expectedStatus, ?array $expectedErrors = null): void
    {
        $response = $this->getJson($this->weatherUrl($queryString));

        $response->assertStatus($expectedStatus);

        if ($expectedErrors) {
            $response->assertJsonValidationErrors($expectedErrors);
        }
    }

    public function test_get_current_weather_success(): void
    {
        $response = $this->getJson($this->weatherUrl('?city=Kyiv'));

        $this->assertWeatherStructure($response);
    }

    public function test_get_current_weather_city_not_found(): void
    {
        $response = $this->getJson($this->weatherUrl('?city=invalid_city_name'));

        $this->assertErrorMessage($response, 'city_not_found');
    }

    public static function weatherValidationProvider(): array
    {
        return [
            'missing city' => ['', 400, ['city']],
            'city too short' => ['?city=A', 400, ['city']],
            'city too long' => ['?city=' . str_repeat('a', 51), 400, ['city']],
        ];
    }

    // SUBSCRIPTION TESTS

    #[DataProvider('subscriptionValidationProvider')]
    public function test_subscribe_validation(array $payload, int $expectedStatus, array $expectedErrors): void
    {
        $response = $this->postJson($this->subscribeUrl(), $payload);

        $response->assertStatus($expectedStatus)
            ->assertJsonValidationErrors($expectedErrors);
    }

    public function test_subscribe_success(): void
    {
        $payload = $this->getValidSubscriptionPayload();

        $response = $this->postJson($this->subscribeUrl(), $payload);

        $response->assertOk()
            ->assertJsonFragment(['success' => true]);
    }

    public function test_subscribe_already_pending(): void
    {
        $payload = $this->getValidSubscriptionPayload();

        $this->postJson($this->subscribeUrl(), $payload); // create first
        $response = $this->postJson($this->subscribeUrl(), $payload); // duplicate

        $response->assertStatus(409)
            ->assertJsonFragment(['message' => self::ERROR_MESSAGES['subscription_pending']]);
    }

    /**
     * @throws ValidationException
     */
    public function test_subscribe_already_exists(): void
    {
        $payload = $this->getValidSubscriptionPayload();
        $subscription = $this->createAndConfirmSubscription($payload);

        $response = $this->postJson($this->subscribeUrl(), $payload);

        $response->assertStatus(409)
            ->assertJsonFragment(['message' => self::ERROR_MESSAGES['email_already_subscribed']]);

        $this->assertTrue($subscription->fresh()->isActive());
    }

    public function test_subscribe_expired_token_resend(): void
    {
        $payload = $this->getValidSubscriptionPayload('expired@gmail.com');

        // Create initial subscription
        $this->postJson($this->subscribeUrl(), $payload)->assertOk();

        $subscription = $this->getPendingSubscription($payload);
        $this->expireConfirmationToken($subscription);

        // Resend should work
        $response = $this->postJson($this->subscribeUrl(), $payload);

        $response->assertOk()
            ->assertJsonFragment(['message' => self::ERROR_MESSAGES['subscription_success']]);

        // Verify new token was created
        $this->assertNewTokenCreated($subscription);
    }

    public function test_subscribe_city_not_found(): void
    {
        $payload = $this->getValidSubscriptionPayload();
        $payload['city'] = 'invalid_city_name';

        $response = $this->postJson($this->subscribeUrl(), $payload);

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => self::ERROR_MESSAGES['city_not_found']]);
    }

    public static function subscriptionValidationProvider(): array
    {
        return [
            'invalid email' => [
                ['email' => 'invalid', 'city' => 'Kyiv', 'frequency' => 'daily'],
                400,
                ['email']
            ],
            'city too short' => [
                ['email' => 'test@gmail.com', 'city' => 'A', 'frequency' => 'daily'],
                400,
                ['city']
            ],
            'city too long' => [
                ['email' => 'test@gmail.com', 'city' => str_repeat('a', 51), 'frequency' => 'daily'],
                400,
                ['city']
            ],
            'invalid frequency' => [
                ['email' => 'test@gmail.com', 'city' => 'Kyiv', 'frequency' => 'monthly'],
                400,
                ['frequency']
            ],
            'empty payload' => [
                [],
                400,
                ['email', 'city', 'frequency']
            ],
        ];
    }

    // CONFIRMATION TESTS

    /**
     * @throws ValidationException
     */
    public function test_confirm_success(): void
    {
        $payload = $this->getValidSubscriptionPayload('subscriber@gmail.com');
        $this->postJson($this->subscribeUrl(), $payload)->assertOk();

        $subscription = $this->getPendingSubscription($payload);
        $confirmationToken = $this->getTokenFromSubscription($subscription, 'confirm');

        $response = $this->getJson($this->confirmUrl($confirmationToken));

        $response->assertOk()
            ->assertJsonFragment(['success' => true]);

        $this->assertTrue($subscription->fresh()->isActive());
    }

   #[DataProvider('tokenValidationProvider')]
    public function test_confirm_subscription_token_validation(string $token, array $expectedErrors): void
    {
        $response = $this->getJson($this->confirmUrl($token));

        $response->assertStatus(400)
            ->assertJsonValidationErrors($expectedErrors);
    }

    public function test_confirm_subscription_token_not_found(): void
    {
        $token = str_repeat('a', 64);

        $response = $this->getJson($this->confirmUrl($token));

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => self::ERROR_MESSAGES['token_not_found']]);
    }

    // UNSUBSCRIBE TESTS

    /**
     * @throws ValidationException
     */
    public function test_unsubscribe_success(): void
    {
        $payload = $this->getValidSubscriptionPayload('unsubscribe@gmail.com');
        $subscription = $this->createAndConfirmSubscription($payload);

        $cancellationToken = $this->getTokenFromSubscription($subscription, 'cancel');

        $response = $this->getJson($this->unsubscribeUrl($cancellationToken));

        $response->assertOk()
            ->assertJsonStructure(['success', 'message']);
    }

    #[DataProvider('tokenValidationProvider')]
    public function test_unsubscribe_token_validation(string $token, array $expectedErrors): void
    {
        $response = $this->getJson($this->unsubscribeUrl($token));

        $response->assertStatus(400)
            ->assertJsonValidationErrors($expectedErrors);
    }

    public function test_unsubscribe_token_not_found(): void
    {
        $token = str_repeat('a', 64);

        $response = $this->getJson($this->unsubscribeUrl($token));

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => self::ERROR_MESSAGES['token_not_found']]);
    }

    public static function tokenValidationProvider(): array
    {
        return [
            'token too short' => ['abc', ['token']],
            'token not hex' => ['z1234567890abcdef...', ['token']],
        ];
    }

    // HELPER METHODS

    private function getValidSubscriptionPayload(string $email = 'user@gmail.com'): array
    {
        return [
            'email' => $email,
            'city' => 'Kyiv',
            'frequency' => 'daily',
        ];
    }

    /**
     * @throws ValidationException
     */
    private function getPendingSubscription(array $payload)
    {
        return $this->repository->getPendingSubscription(
            new Subscription(
                email: new Email($payload['email']),
                city: new City($payload['city']),
                frequency: Frequency::fromName($payload['frequency'])
            )
        );
    }

    private function getTokenFromSubscription($subscription, string $type): string
    {
        $token = $subscription->tokens
            ->firstWhere('type', $type)
            ?->token;

        $this->assertNotNull($token, "Token of type '{$type}' not found");

        return $token;
    }

    /**
     * @throws ValidationException
     */
    private function createAndConfirmSubscription(array $payload)
    {
        $this->postJson($this->subscribeUrl(), $payload)->assertOk();

        $subscription = $this->getPendingSubscription($payload);
        $confirmationToken = $this->getTokenFromSubscription($subscription, 'confirm');

        $this->getJson($this->confirmUrl($confirmationToken))->assertOk();

        return $subscription;
    }

    private function expireConfirmationToken($subscription): void
    {
        $tokenModel = $subscription->tokens->firstWhere('type', 'confirm');
        $tokenModel->update(['expires_at' => now()->subHour()]);
    }

    private function assertNewTokenCreated($subscription): void
    {
        $subscription->refresh();
        $newToken = $subscription->tokens()
            ->where('type', 'confirm')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        $this->assertNotNull($newToken, 'New confirmation token was not created');
    }

    // URL BUILDERS

    private function weatherUrl(string $queryString = ''): string
    {
        return "/api/weather{$queryString}";
    }

    private function subscribeUrl(): string
    {
        return '/api/subscribe';
    }

    private function confirmUrl(string $token): string
    {
        return "/api/confirm/{$token}";
    }

    private function unsubscribeUrl(string $token): string
    {
        return "/api/unsubscribe/{$token}";
    }

    // ASSERTION HELPERS

    private function assertSuccessResponse($response): void
    {
        $response->assertOk()
            ->assertJsonFragment(['success' => true]);
    }

    private function assertErrorMessage($response, string $messageKey, int $statusCode = 404): void
    {
        $response->assertStatus($statusCode)
            ->assertJsonFragment(['message' => self::ERROR_MESSAGES[$messageKey]]);
    }

    private function assertWeatherStructure($response): void
    {
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'temperature',
                    'humidity',
                    'description',
                ]
            ]);
    }
}
