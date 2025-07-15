<?php

namespace Tests\Unit\Application\Subscription\Commands;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Exceptions\Custom\EmailAlreadySubscribedException;
use App\Exceptions\Custom\FrequencyNotFoundException;
use App\Exceptions\Custom\SubscriptionAlreadyPendingException;
use App\Exceptions\ValidationException;
use App\Modules\Subscription\Application\Commands\CreateSubscriptionCommand;
use App\Modules\Subscription\Application\DTOs\CreateSubscriptionRequestDTO;
use App\Modules\Subscription\Application\Services\SubscriptionService;
use App\Modules\Subscription\Domain\Entities\Subscription;
use App\Modules\Subscription\Domain\ValueObjects\Token\Factory\TokenFactory;
use App\Modules\Subscription\Domain\ValueObjects\Token\Factory\TokenFactoryInterface;
use App\Modules\Subscription\Domain\ValueObjects\Token\Token;
use App\Modules\Subscription\Infrastructure\Token\Generator\TokenGenerator;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use Throwable;

class CreateSubscriptionCommandTest extends TestCase
{
    private array $dtoData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dtoData = [
            'email' => 'test@example.com',
            'city' => 'Kyiv',
            'frequency' => 'daily',
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @throws ValidationException
     * @throws ApiAccessException
     * @throws EmailAlreadySubscribedException
     * @throws SubscriptionAlreadyPendingException
     * @throws FrequencyNotFoundException
     * @throws CityNotFoundException
     * @throws RandomException
     */
    public function test_subscription_created_successfully(): void
    {
        $dto = new CreateSubscriptionRequestDTO(
            $this->dtoData['email'],
            $this->dtoData['city'],
            $this->dtoData['frequency']
        );

        $token = $this->generateConfirmationToken();

        $subscription = Mockery::mock(Subscription::class);
        $subscription->shouldReceive('getConfirmationToken')
            ->once()
            ->andReturn($token);

        $subscriptionService = Mockery::mock(SubscriptionService::class);
        $subscriptionService->shouldReceive('subscribe')
            ->once()
            ->withArgs(function ($email, $city, $frequency) {
                return $email->getValue() === 'test@example.com' &&
                    $city->getName() === 'Kyiv' &&
                    $frequency->getName() === 'daily';
            })
            ->andReturn($subscription);

        $command = new CreateSubscriptionCommand($subscriptionService);
        $result = $command->execute($dto);

        $this->assertEquals($token->getValue(), $result);
    }

    #[DataProvider('exceptionProvider')]
    public function test_exceptions_are_thrown(Throwable $exception): void
    {
        $this->expectException(get_class($exception));

        $dto = new CreateSubscriptionRequestDTO(
            $this->dtoData['email'],
            $this->dtoData['city'],
            $this->dtoData['frequency']
        );

        $subscriptionService = Mockery::mock(SubscriptionService::class);
        $subscriptionService->shouldReceive('subscribe')
            ->once()
            ->andThrow($exception);

        $command = new CreateSubscriptionCommand($subscriptionService);
        $command->execute($dto);
    }

    private function generateConfirmationToken(): Token
    {
        /**
         * @var TokenFactoryInterface $factory
         */
        $factory = new TokenFactory(new TokenGenerator());
        return $factory->createConfirmation();
    }

    public static function exceptionProvider(): array
    {
        return [
            'validation' => [new ValidationException(['email' => ['Invalid email']])],
            'city_not_found' => [new CityNotFoundException()],
            'api_error' => [new ApiAccessException()],
            'frequency_missing' => [new FrequencyNotFoundException()],
            'already_pending' => [new SubscriptionAlreadyPendingException()],
            'already_subscribed' => [new EmailAlreadySubscribedException()],
        ];
    }
}
