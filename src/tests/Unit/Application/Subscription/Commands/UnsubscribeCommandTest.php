<?php

namespace Tests\Unit\Application\Subscription\Commands;

use App\Application\Subscription\Commands\UnsubscribeCommand;
use App\Application\Subscription\DTOs\UnsubscribeRequestDTO;
use App\Application\Subscription\Services\SubscriptionService;
use App\Domain\Subscription\ValueObjects\Token\Factory\TokenFactory;
use App\Domain\Subscription\ValueObjects\Token\Factory\TokenFactoryInterface;
use App\Domain\Subscription\ValueObjects\Token\Token;
use App\Exceptions\Custom\TokenNotFoundException;
use App\Exceptions\ValidationException;
use App\Infrastructure\Subscription\Token\Generator\TokenGenerator;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class UnsubscribeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @throws TokenNotFoundException
     * @throws ValidationException
     * @throws RandomException
     */
    public function test_unsubscribe_successful(): void
    {
        $dto = new UnsubscribeRequestDTO(
            $this->generateUnsubscribeToken()
        );

        $subscriptionService = Mockery::mock(SubscriptionService::class);
        $subscriptionService->shouldReceive('unsubscribe')
            ->once()
            ->with($dto)
            ->andReturn(true);

        $command = new UnsubscribeCommand($subscriptionService);
        $result = $command->execute($dto);

        $this->assertTrue($result);
    }

    /**
     * @throws TokenNotFoundException
     * @throws ValidationException
     * @throws RandomException
     */
    #[DataProvider('exceptionProvider')]
    public function test_exceptions_are_thrown(string $exceptionClass): void
    {
        $this->expectException($exceptionClass);

        $dto = new UnsubscribeRequestDTO(
            $this->generateUnsubscribeToken()
        );

        $subscriptionService = Mockery::mock(SubscriptionService::class);
        $subscriptionService->shouldReceive('unsubscribe')
            ->once()
            ->with($dto)
            ->andThrow($exceptionClass);

        $command = new UnsubscribeCommand($subscriptionService);
        $command->execute($dto);
    }

    private function generateUnsubscribeToken(): Token
    {
        /**
         * @var TokenFactoryInterface $factory
         */
        $factory = new TokenFactory(new TokenGenerator());
        return $factory->createCancel();
    }

    public static function exceptionProvider(): array
    {
        return [
            [TokenNotFoundException::class],
        ];
    }
}
