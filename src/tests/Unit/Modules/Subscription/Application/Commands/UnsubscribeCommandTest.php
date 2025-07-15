<?php

namespace Tests\Unit\Modules\Subscription\Application\Commands;

use App\Exceptions\Custom\TokenNotFoundException;
use App\Exceptions\ValidationException;
use App\Modules\Subscription\Application\Commands\UnsubscribeCommand;
use App\Modules\Subscription\Application\DTOs\UnsubscribeRequestDTO;
use App\Modules\Subscription\Application\Services\SubscriptionService;
use App\Modules\Subscription\Domain\ValueObjects\Token\Factory\TokenFactory;
use App\Modules\Subscription\Domain\ValueObjects\Token\Factory\TokenFactoryInterface;
use App\Modules\Subscription\Domain\ValueObjects\Token\Token;
use App\Modules\Subscription\Infrastructure\Token\Generator\TokenGenerator;
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
