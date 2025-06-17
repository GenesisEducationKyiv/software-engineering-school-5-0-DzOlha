<?php

namespace Application\Subscription\Commands;

use App\Application\Subscription\Commands\ConfirmSubscriptionCommand;
use App\Application\Subscription\DTOs\ConfirmSubscriptionRequestDTO;
use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Subscription\Services\SubscriptionService;
use App\Exceptions\Custom\TokenNotFoundException;
use PHPUnit\Framework\TestCase;
use Mockery;

class ConfirmSubscriptionCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_subscription_confirmed_successfully(): void
    {
        $dto = Mockery::mock(ConfirmSubscriptionRequestDTO::class);
        $subscription = Mockery::mock(Subscription::class);

        $subscriptionService = Mockery::mock(SubscriptionService::class);
        $subscriptionService
            ->shouldReceive('confirmSubscription')
            ->once()
            ->with($dto)
            ->andReturn($subscription);

        $command = new ConfirmSubscriptionCommand($subscriptionService);
        $result = $command->execute($dto);

        $this->assertTrue($result);
    }

    public function test_subscription_not_found(): void
    {
        $dto = Mockery::mock(ConfirmSubscriptionRequestDTO::class);

        $subscriptionService = Mockery::mock(SubscriptionService::class);
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

        $subscriptionService = Mockery::mock(SubscriptionService::class);
        $subscriptionService
            ->shouldReceive('confirmSubscription')
            ->once()
            ->with($dto)
            ->andThrow(TokenNotFoundException::class);

        $command = new ConfirmSubscriptionCommand($subscriptionService);
        $command->execute($dto);
    }
}
