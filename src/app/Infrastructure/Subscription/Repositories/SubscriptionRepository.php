<?php

namespace App\Infrastructure\Subscription\Repositories;

use App\Domain\Subscription\Entities\Subscription as SubscriptionEntity;
use App\Domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use App\Domain\Subscription\ValueObjects\Email;
use App\Domain\Subscription\ValueObjects\Frequency as FrequencyValueObject;
use App\Domain\Subscription\ValueObjects\Status;
use App\Domain\Subscription\ValueObjects\Token as TokenValueObject;
use App\Domain\Weather\ValueObjects\City as CityValueObject;
use App\Exceptions\Custom\FrequencyNotFoundException;
use App\Infrastructure\Subscription\Models\City;
use App\Infrastructure\Subscription\Models\Frequency;
use App\Infrastructure\Subscription\Models\Subscription;
use App\Infrastructure\Subscription\Models\SubscriptionToken;
use App\Infrastructure\Subscription\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    /**
     * @param SubscriptionEntity $subscriptionEntity
     * @return SubscriptionEntity
     * @throws FrequencyNotFoundException
     */
    public function save(SubscriptionEntity $subscriptionEntity): SubscriptionEntity
    {
        return DB::transaction(function () use ($subscriptionEntity) {
            $user = User::firstOrCreate([
                'email' => $subscriptionEntity->getEmail()->getValue()
            ]);

            $city = City::firstOrCreate([
                'name' => $subscriptionEntity->getCity()->getName()
            ]);

            $frequency = Frequency::where('name', $subscriptionEntity->getFrequency()->getName())->first();
            if (!$frequency) {
                throw new FrequencyNotFoundException();
            }

            $subscription = new Subscription([
                'user_id'      => $user->id,
                'city_id'      => $city->id,
                'frequency_id' => $frequency->id,
                'status'       => $subscriptionEntity->getStatus()->getValue()
            ]);
            $subscription->save();

            $subscriptionEntity->setId($subscription->id);

            if ($subscriptionEntity->getConfirmationToken() !== null) {
                $this->saveToken(
                    $subscription->id,
                    $subscriptionEntity->getConfirmationToken()->getValue(),
                    'confirm',
                    $subscriptionEntity->getConfirmationToken()->getExpiresAt()
                );
            }

            if ($subscriptionEntity->getUnsubscribeToken() !== null) {
                $this->saveToken(
                    $subscription->id,
                    $subscriptionEntity->getUnsubscribeToken()->getValue(),
                    'cancel'
                );
            }

            return $subscriptionEntity;
        });
    }

    public function findSubscriptionById(int $id): ?SubscriptionEntity
    {
        /** @var Subscription|null $subscription */
        $subscription = Subscription::with([
            'user',
            'city',
            'frequency',
            'tokens' => function (HasMany $query) {
                $query->whereIn('type', ['confirm', 'cancel'])
                    ->where(function (Builder $q) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    });
            },
        ])->find($id);

        if (!$subscription || !$subscription->user || !$subscription->city || !$subscription->frequency) {
            return null;
        }

        $subscriptionEntity = new SubscriptionEntity(
            new Email($subscription->user->email),
            new CityValueObject($subscription->city->name),
            FrequencyValueObject::fromId(
                $subscription->frequency->id,
                $subscription->frequency->name,
                $subscription->frequency->interval_minutes
            ),
            Status::fromString($subscription->status)
        );
        $subscriptionEntity->setId($subscription->id);

        $tokens = $subscription->tokens ?? collect();

        /**
         * @var ?SubscriptionToken $confirmToken
         */
        $confirmToken = $tokens->firstWhere('type', 'confirm');

        /**
         * @var ?SubscriptionToken $cancelToken
         */
        $cancelToken  = $tokens->firstWhere('type', 'cancel');

        if ($confirmToken) {
            $expiresAt = $confirmToken->expires_at ? new \DateTimeImmutable($confirmToken->expires_at) : null;

            $subscriptionEntity->setConfirmationToken(new TokenValueObject(
                $confirmToken->token,
                $confirmToken->type,
                $expiresAt
            ));
        }

        if ($cancelToken) {
            $subscriptionEntity->setUnsubscribeToken(new TokenValueObject(
                $cancelToken->token,
                $cancelToken->type,
                null
            ));
        }

        return $subscriptionEntity;
    }

    private function saveToken(
        int $subscriptionId,
        string $token,
        string $type,
        ?\DateTimeInterface $expiresAt = null
    ): void {
        SubscriptionToken::create([
            'subscription_id' => $subscriptionId,
            'token'           => $token,
            'type'            => $type,
            'expires_at'      => $expiresAt
        ]);
    }

    public function hasActiveSubscription(SubscriptionEntity $subscription): bool
    {
        return Subscription::query()
            ->join('users', 'subscriptions.user_id', '=', 'users.id')
            ->join('cities', 'subscriptions.city_id', '=', 'cities.id')
            ->join('frequencies', 'subscriptions.frequency_id', '=', 'frequencies.id')
            ->where('users.email', $subscription->getEmail()->getValue())
            ->where('cities.name', $subscription->getCity()->getName())
            ->where('frequencies.name', $subscription->getFrequency()->getName())
            ->where('subscriptions.status', 'active')
            ->exists();
    }

    public function getPendingSubscription(SubscriptionEntity $subscription): ?Subscription
    {
        return Subscription::query()
            ->join('users', 'subscriptions.user_id', '=', 'users.id')
            ->join('cities', 'subscriptions.city_id', '=', 'cities.id')
            ->join('frequencies', 'subscriptions.frequency_id', '=', 'frequencies.id')
            ->where('users.email', $subscription->getEmail()->getValue())
            ->where('cities.name', $subscription->getCity()->getName())
            ->where('frequencies.name', $subscription->getFrequency()->getName())
            ->where('subscriptions.status', 'pending')
            ->select('subscriptions.*')
            ->first();
    }

    public function hasValidConfirmationToken(int $subscriptionId): bool
    {
        return SubscriptionToken::query()
            ->where('subscription_id', $subscriptionId)
            ->where('type', 'confirm')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    public function replaceTokensForPending(
        int $subscriptionId,
        TokenValueObject $confirm,
        TokenValueObject $cancel
    ): void {
        SubscriptionToken::query()
            ->where('subscription_id', $subscriptionId)
            ->delete();

        SubscriptionToken::insert([
            [
                'subscription_id' => $subscriptionId,
                'token'           => $confirm->getValue(),
                'type'            => 'confirm',
                'expires_at'      => $confirm->getExpiresAt(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'subscription_id' => $subscriptionId,
                'token'           => $cancel->getValue(),
                'type'            => 'cancel',
                'expires_at'      => $cancel->getExpiresAt(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }

    public function delete(int $subscriptionId): bool
    {
        return Subscription::query()
                ->where('id', $subscriptionId)
                ->delete() > 0;
    }

    public function confirmSubscriptionByToken(string $token): ?SubscriptionEntity
    {
        return DB::transaction(function () use ($token) {
            $tokenModel = SubscriptionToken::where('token', $token)
                ->where('type', 'confirm')
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->first();

            if (!$tokenModel) {
                return null;
            }

            $updated = Subscription::where('id', $tokenModel->subscription_id)
                ->update(['status' => 'active']);

            if (!$updated) {
                return null;
            }

            SubscriptionToken::where('id', $tokenModel->id)->delete();

            return $this->findSubscriptionById($tokenModel->subscription_id);
        });
    }

    public function unsubscribeByToken(string $token): ?SubscriptionEntity
    {
        return DB::transaction(function () use ($token) {
            $tokenModel = SubscriptionToken::where('token', $token)
                ->where('type', 'cancel')
                ->first();

            if (!$tokenModel) {
                return null;
            }

            $subscriptionEntity = $this->findSubscriptionById($tokenModel->subscription_id);

            if (!$subscriptionEntity) {
                return null;
            }

            Subscription::where('id', $tokenModel->subscription_id)->delete();

            return $subscriptionEntity;
        });
    }
}
