<?php

namespace Application\Subscription\Commands;

use App\Exceptions\Custom\TokenNotFoundException;
use App\Modules\Subscription\Application\Commands\ConfirmSubscriptionCommand;
use App\Modules\Subscription\Application\DTOs\ConfirmSubscriptionRequestDTO;
use App\Modules\Subscription\Application\Services\SubscriptionServiceInterface;
use App\Modules\Subscription\Domain\Entities\Subscription;
use Mockery;
use PHPUnit\Framework\TestCase;

class ConfirmSubscriptionCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @throws TokenNotFoundException
     */
    public function test_subscription_confirmed_successfully(): void
    {
        $dto = Mockery::mock(ConfirmSubscriptionRequestDTO::class);
        $subscription = Mockery::mock(Subscription::class);

        $subscriptionService = Mockery::mock(SubscriptionServiceInterface::class);
        $subscriptionService
            ->shouldReceive('confirmSubscription')
            ->once()
            ->with($dto)
            ->andReturn($subscription);

        $command = new ConfirmSubscriptionCommand($subscriptionService);
        $result = $command->execute($dto);

        $this->assertTrue($result);
    }

    /**
     * @throws TokenNotFoundException
     */
    public function test_subscription_not_found(): void
    {
        $dto = Mockery::mock(ConfirmSubscriptionRequestDTO::class);

        $subscriptionService = Mockery::mock(SubscriptionServiceInterface::class);
        $subscriptionService
            ->shouldReceive('confirmSubscription')
            ->once()
            ->with($dto)
            ->andReturn(null);

        $command = new ConfirmSubscriptionCommand($subscriptionService);
        $result = $command->execute($dto);

        $this->assertFalse($result);
    }

    public function test_token_not_found_exception_is_thrown(): void
    {
        $this->expectException(TokenNotFoundException::class);

        $dto = Mockery::mock(ConfirmSubscriptionRequestDTO::class);

        $subscriptionService = Mockery::mock(SubscriptionServiceInterface::class);
        $subscriptionService
            ->shouldReceive('confirmSubscription')
            ->once()
            ->with($dto)
            ->andThrow(TokenNotFoundException::class);

        $command = new ConfirmSubscriptionCommand($subscriptionService);
        $command->execute($dto);
    }
}
